<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Purchase;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Pembelian Barang';
        $setting = Toko::first();

        $data = [
            'setting' => $setting,
            'title' => $title,
        ];
        return view('purchase.index', $data);
    }

    public function indexData()
    {
        $purchases = Purchase::with('supplier')->orderBy('created_at', 'desc')->get();
        return ResponseFormatter::success(
            [
                'purchases' => $purchases,
            ],
            'Data pembelian berhasil diambil'
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Tambah Pembelian Barang';
        $setting = Toko::first();

        $data = [
            'setting' => $setting,
            'title' => $title,
        ];
        return view('purchase.create', $data);
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
            'supplier_id' => 'required|exists:t_supplier,IdSupplier'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'message' => $validated->errors()->first()
                ],
                'Validasi gagal',
                422
            );
        }

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'total' => 0,
            'amount' => 0,
            // 'status' => 'pending'
        ]);

        return ResponseFormatter::success(
            [
                'purchase' => $purchase,
                'redirect' => route('purchase.detail.create', $purchase->id)
            ],
            'Pembelian berhasil ditambahkan'
        );
    }

    public function createDetail(Purchase $purchase)
    {
        $title = 'POS TOKO | Tambah Detail Pembelian';
        $setting = Toko::first();

        $data = [
            'setting' => $setting,
            'title' => $title,
            'purchase' => $purchase
        ];
        return view('purchase.create-purchase-detail', $data);
    }

    public function detail(Purchase $purchase)
    {
        $purchase->load('purchaseDetails', 'purchaseDetails.product');
        return ResponseFormatter::success(
            [
                'purchase' => $purchase
            ],
            'Data detail pembelian berhasil diambil'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
