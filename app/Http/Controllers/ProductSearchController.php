<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\ProductSearch;
use App\Models\Toko;
use Illuminate\Http\Request;
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
            'currentNav' => 'productSearch',
        ];

        return view('product.productSearch', $data);
    }

    public function indexData(Request $request)
    {
        $search = $request->search;
        // $products = ProductSearch::where('name', 'like', '%' . $search . '%')
        //     ->get();
        $products = ProductSearch::get();

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
            'product_id' => 'requiredIf:name,null|exists:t_barang,IdBarang',
            'name' => 'requiredIf:product_id,null',
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return ResponseFormatter::error([
                'message' => $validate->errors()->first(),
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
                    throw new \Exception(json_encode($data['data']['error']), $response->status());
                }
            }

            return ResponseFormatter::success([
                'product_id' => $productId,
                'name' => $productName,
            ], 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], 'Data gagal ditambahkan', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductSearch  $productSearch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $productSearch = ProductSearch::where('product_id', $product->IdBarang)
                ->first();

            $productSearch->delete();

            if (env('HOSTING_DOMAIN') != 'hosting') {
                $response = Http::delete(env('HOSTING_DOMAIN') . '/api/barang-dicari/' . $product->IdBarang);
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
}