<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\ProductHasExpiredBefore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductHasExpiredBeforeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Barang Pernah Kadaluarsa';

        $data = [
            'title' => $title,
            'typeReport' => 'Bulanan',
            'currentNav' => 'productHasExpiredBefore',
        ];

        return view('product.hasExpiredBefore', $data);
    }

    public function indexData(): JsonResponse
    {
        $products = ProductHasExpiredBefore::with('product')
            ->orderBy('expired_date', 'desc')
            ->get();

        return ResponseFormatter::success([
            'products' => $products
        ], 'Data berhasil diambil');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product, Request $request)
    {
        $rules = [
            'expiredDate' => 'required|date',
            'quantity' => 'required|integer',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'message' => $validated->errors()->first()
            ], 'Data gagal divalidasi', 422);
        }

        try {
            DB::beginTransaction();

            // mengecek stok produk
            if ($product->stok < $request->quantity) {
                return ResponseFormatter::error([
                    'error' => 'Stok produk tidak mencukupi'
                ], 'Data gagal disimpan', 422);
            }

            // edit stok produk
            $product->stok = $product->stok - $request->quantity;

            // jika stok produk habis, maka hapus tanggal kadaluarsa produk
            if ($product->stok == 0) {
                $product->expDate = null;
            }
            $product->save();

            // hitung kerugian
            $loss = $product->hargaPokok * $request->quantity;

            // simpan data barang kadaluarsa
            ProductHasExpiredBefore::create([
                'product_id' => $product->IdBarang,
                'expired_date' => $request->expiredDate,
                'quantity' => $request->quantity,
                'loss' => $loss,
            ]);

            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return ResponseFormatter::error([
                'message' => $e->getMessage()
            ], 'Data gagal disimpan', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductHasExpiredBefore  $productHasExpiredBefore
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductHasExpiredBefore $productHasExpiredBefore)
    {
        try {
            DB::beginTransaction();

            // edit stok produk
            $product = Product::find($productHasExpiredBefore->product_id);
            $product->stok = $product->stok + $productHasExpiredBefore->quantity;
            $product->save();

            // hapus data barang kadaluarsa
            $productHasExpiredBefore->delete();

            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return ResponseFormatter::error([
                'message' => $e->getMessage()
            ], 'Data gagal dihapus', 500);
        }
    }
}
