<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shopping;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ShoppingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Belanja';
        $categories = Category::all();

        $data = [
            'title' => $title,
            'categories' => $categories
        ];

        return view('purchase.shopping', $data);
    }

    /**
     * Menampilkan data permintaan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(Request $request)
    {
        $shoppingProducts = Shopping::with('product:IdBarang,stok')
            ->select('id', 'IdBarang', 'nmBarang', 'satuan', 'hargaPokok', 'jumlah', 'total')
            ->whereHas('product', function ($query) use ($request) {
                return $query->when($request->filterProduct != null, function ($query) use ($request) {
                    return $query->where('nmBarang', 'LIKE', '%' . $request->input('filterProduct') . '%')
                        ->orWhere('IdBarang', 'LIKE', '%' . $request->input('filterProduct') . '%');
                })
                    ->when($request->filterCategory != null, function ($query) use ($request) {
                        return $query->where('jenis', $request->input('filterCategory'));
                    })
                    ->when($request->filterMerk != null, function ($query) use ($request) {
                        return $query->where('merk_id', $request->input('filterMerk'));
                    });
            })
            ->orderBy('nmBarang', 'desc')
            ->get();

        return ResponseFormatter::success([
            'shoppingProducts' => $shoppingProducts
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
            'IdBarang' => 'required|exists:t_barang,IdBarang|unique:t_belanja,IdBarang',
            'qty' => 'required|numeric|min:2'
        ];

        $messages = [
            'IdBarang.required' => 'Barang tidak boleh kosong',
            'IdBarang.exists' => 'Barang tidak ditemukan',
            'IdBarang.unique' => 'Barang sudah ada di daftar belanja'
        ];

        $validated = Validator::make($request->all(), $rules, $messages);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'error' => $validated->errors()->first()
            ], 'Data gagal divalidasi', 422);
        }

        try {
            DB::beginTransaction();
            $product = Product::where('IdBarang', $request->IdBarang)->first();
            $qty = $request->qty ?? 2;

            $purchase = Shopping::create([
                'IdBarang' => $product->IdBarang,
                'nmBarang' => $product->nmBarang,
                'satuan' => $product->satuan,
                'hargaPokok' => $product->hargaPokok,
                'jumlah' => $qty,
                'TOTAL' => $product->hargaPokok * $qty,
            ]);

            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal disimpan', 500);
        }
    }

    /**
     * Edit the specified resource.
     *
     * @param  \App\Models\Shopping  $shopping
     * @return \Illuminate\Http\Response
     */
    public function edit(Shopping $shopping)
    {
        $units = Unit::orderBy('satuan')->get();

        return ResponseFormatter::success([
            'shoppingProduct' => $shopping->load('product:IdBarang,stok'),
            'units' => $units
        ], 'Data berhasil diambil');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shopping $shopping)
    {
        $rules = [
            'jumlah' => 'required|numeric|min:1',
            'hargaPokok' => 'required|numeric|min:1',
            'stok' => 'required|numeric|min:0'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'error' => $validated->errors()->first()
            ], 'Data gagal divalidasi', 422);
        }

        try {
            DB::beginTransaction();
            $shopping->update([
                'jumlah' => $request->jumlah,
                'hargaPokok' => $request->hargaPokok,
                'TOTAL' => $request->jumlah * $request->hargaPokok
            ]);

            Product::where('IdBarang', $shopping->IdBarang)->update([
                'stok' => $request->stok
            ]);
            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal diubah', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shopping $shopping)
    {
        try {
            DB::beginTransaction();
            $shopping->delete();
            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal dihapus', 500);
        }
    }

    public function uploadData()
    {
        $shopping = DB::table('t_belanja')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/shopping', [
            'shopping' => $shopping
        ]);

        $data = $response->json();
        if ($response->successful()) {
            return ResponseFormatter::success(null, 'Data berhasil diupload');
        } else {
            return ResponseFormatter::error([
                'error' => $data['data']['error']
            ], 'Data gagal diupload', $response->status());
        }
    }

    public function downloadData()
    {
        try {
            $response = Http::get(env('HOSTING_DOMAIN') . '/api/download-data/shopping');

            $data = $response->json();
            if ($response->successful()) {
                $shoppings = $data['data']['shoppings'];

                // menghapus data lama
                Shopping::truncate();

                // menyimpan data baru
                foreach ($shoppings as $shopping) {
                    Shopping::create([
                        'IdBarang' => $shopping['IdBarang'],
                        'nmBarang' => $shopping['nmBarang'],
                        'satuan' => $shopping['satuan'],
                        'hargaPokok' => $shopping['hargaPokok'],
                        'jumlah' => $shopping['jumlah'],
                        'TOTAL' => $shopping['TOTAL']
                    ]);
                }

                return ResponseFormatter::success(null, 'Data berhasil diambil');
            } else {
                throw new \Exception($data['data']['error'], $response->status());
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal diambil', $e->getCode() != 0 ? $e->getCode() : 500);
        }
    }
}
