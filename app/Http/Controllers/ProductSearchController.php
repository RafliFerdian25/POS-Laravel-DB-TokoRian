<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\ProductSearch;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProductSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Barang Dicari';
        $setting = Toko::first();

        $data = [
            'setting' => $setting,
            'title' => $title,
            'typeReport' => 'Bulanan',
            'currentNav' => 'productSearch',
        ];

        return view('product.searched', $data);
    }

    public function indexData(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1])->addDay();
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $threeMonthAgo = null;
        if ($typeReport == 'Bulanan') {
            $threeMonthAgo = $date->copy()->subMonths(3);
        }
        $products = ProductSearch::select('product_id', 'name', DB::raw('COUNT(name) as total'))
            ->when($typeReport == 'Bulanan', function ($query) use ($date, $threeMonthAgo) {
                return $query->whereBetween('created_at', [$threeMonthAgo->startOfMonth(), $date->copy()->endOfMonth()]);
            }, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id', 'name')
            ->get();

        return ResponseFormatter::success([
            'products' => $products,
        ], 'Data berhasil diambil');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'nullable|requiredIf:name,null|exists:t_barang,IdBarang',
            'name' => 'nullable|requiredIf:product_id,null',
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return ResponseFormatter::error([
                'error' => $validate->errors()->first(),
            ], 'Data gagal divalidasi', 422);
        }

        try {
            if ($request->product_id) {
                $product = Product::where('IdBarang', $request->product_id)
                    ->first(['IdBarang', 'nmBarang']);
                $productId = $product->IdBarang;
                $productName = $product->nmBarang;
            } else if ($request->name) {
                $productId = null;
                $productName = $request->name;
            }

            DB::beginTransaction();

            ProductSearch::create([
                'product_id' => $productId,
                'name' => $productName,
            ]);

            if (env('HOSTING_DOMAIN') != 'hosting') {
                $response = Http::post(env('HOSTING_DOMAIN') . '/api/barang-dicari', [
                    'product_id' => $productId,
                    'name' => $productName,
                ]);
                $data = $response->json();

                if (!$response->successful()) {
                    throw new \Exception(json_encode($data['data']['error'], JSON_PRETTY_PRINT), $response->status());
                }
            }

            DB::commit();
            return ResponseFormatter::success([
                'product_id' => $productId,
                'name' => $productName,
            ], 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage(),
            ], 'Data gagal ditambahkan', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductSearch  $productSearch
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        try {
            $productSearch = ProductSearch::where('name', $name)
                ->delete();

            if (env('HOSTING_DOMAIN') != 'hosting') {
                $response = Http::delete(env('HOSTING_DOMAIN') . '/api/barang-dicari/' . $name);
                $data = $response->json();

                if (!$response->successful()) {
                    throw new \Exception(json_encode($data['data']['error']), $response->status());
                }
            }

            return ResponseFormatter::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Data gagal dihapus', 500);
        }
    }

    public function downloadData()
    {
        try {
            $response = Http::get(env('HOSTING_DOMAIN') . '/api/download-data/barang-dicari');

            $data = $response->json();
            if ($response->successful()) {
                $productSearches = $data['data']['productSearches'];

                // menghapus data lama
                ProductSearch::truncate();

                // menyimpan data baru
                foreach ($productSearches as $productSearch) {
                    ProductSearch::create([
                        'product_id' => $productSearch['product_id'],
                        'name' => $productSearch['name'],
                    ]);
                }

                return ResponseFormatter::success(null, 'Data berhasil diambil');
            } else {
                throw new \Exception($data['data']['error'], $response->status());
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal diambil', 500);
        }
    }
}
