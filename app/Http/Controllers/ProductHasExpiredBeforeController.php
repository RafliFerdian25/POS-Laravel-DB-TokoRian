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
            'expired_date' => 'required|date',
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
                    'message' => 'Stok produk tidak mencukupi'
                ], 'Data gagal disimpan', 422);
            }

            // edit stok produk
            $product->stok = $product->stok - $request->quantity;
            $product->save();

            // hitung kerugian
            $loss = $product->cost_of_goods_sold * $request->quantity;

            // simpan data barang kadaluarsa
            ProductHasExpiredBefore::create([
                'product_id' => $product->IdBarang,
                'expired_date' => $request->expired_date,
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
     * Display the specified resource.
     *
     * @param  \App\Models\ProductHasExpiredBefore  $productHasExpiredBefore
     * @return \Illuminate\Http\Response
     */
    public function show(ProductHasExpiredBefore $productHasExpiredBefore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductHasExpiredBefore  $productHasExpiredBefore
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductHasExpiredBefore $productHasExpiredBefore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductHasExpiredBefore  $productHasExpiredBefore
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductHasExpiredBefore $productHasExpiredBefore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductHasExpiredBefore  $productHasExpiredBefore
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductHasExpiredBefore $productHasExpiredBefore)
    {
        //
    }
}
