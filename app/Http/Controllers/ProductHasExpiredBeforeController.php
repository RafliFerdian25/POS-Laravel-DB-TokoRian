<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\ProductHasExpiredBefore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        //
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