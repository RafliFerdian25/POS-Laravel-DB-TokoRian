<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Barcode;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $purchases = Purchase::with('supplier')->orderBy('id', 'desc')->get();
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

        try {
            DB::beginTransaction();
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'total' => 0,
                'amount' => 0,
                // 'status' => 'pending'
            ]);
            DB::commit();
            return ResponseFormatter::success(
                [
                    'purchase' => $purchase,
                    'redirect' => route('purchase.detail.create', $purchase->id)
                ],
                'Pembelian berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Gagal menambahkan pembelian'
                ],
                'Gagal menambahkan pembelian',
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();
            foreach ($purchase->purchaseDetails as $purchaseDetail) {
                $this->destroyDetail($purchaseDetail);
            }
            $purchase->delete();
            DB::commit();
            // dd($purchase->purchaseDetails);
            return ResponseFormatter::success(
                null,
                'Pembelian berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Gagal menghapus pembelian',
                    'error' => $e->getMessage()
                ],
                'Gagal menghapus pembelian',
                500
            );
        }
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

    public function detailData(Purchase $purchase)
    {
        $purchaseDetails = PurchaseDetail::with('product')->where('purchase_id', $purchase->id)->get();
        return ResponseFormatter::success(
            [
                'purchaseDetails' => $purchaseDetails
            ],
            'Data detail pembelian berhasil diambil'
        );
    }

    private function updateProductForDetailPurchase($product, $purchase, $purchaseDetail, $request)
    {
        // Update total pembelian
        $purchase->amount += $purchaseDetail->sub_amount;
        $purchase->total += $purchaseDetail->quantity;

        $purchase->save();

        // update tanggal kadaluarsa produk
        if ($product->expDate == null || $product->expDate > $request->exp_date || $product->stok <= 0) {
            $product->expDate = $request->exp_date;
        }

        // update harga pokok
        if ($product->hargaPokok < $request->cost_of_good_sold || $product->stok <= 0) {
            $product->hargaPokok = $request->cost_of_good_sold;
        }

        if ($product->stok < 0) {
            $product->stok = ($product->stok + ($product->stok * -1)) + $request->quantity;
        } else {
            $product->stok += $request->quantity;
        }

        // update data produk
        $product->save();
    }

    public function storeDetail(Purchase $purchase, Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:t_barang,IdBarang|unique:t_pembelian_detail,product_id,NULL,id,purchase_id,' . $purchase->id,
            'cost_of_good_sold' => 'required|numeric',
            'quantity' => 'required|numeric',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()->first()
                ],
                'Validasi gagal',
                422
            );
        }

        try {
            DB::beginTransaction();
            $product = Product::find($request->product_id);

            $purchaseDetail = PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $request->product_id,
                'quantity' =>  $request->quantity,
                'exp_date' => $request->exp_date,
                'exp_date_old' => $product->expDate,
                'cost_of_good_sold' => $request->cost_of_good_sold,
                'cost_of_good_sold_old' => $product->hargaPokok,
                'sub_amount' => $request->cost_of_good_sold * $request->quantity,
            ]);

            // update data barang
            $this->updateProductForDetailPurchase($product, $purchase, $purchaseDetail, $request);

            // cetak harga
            $productController = new ProductController();
            $newRequest = new Request([
                'IdBarang' => $product->IdBarang,
            ]);
            $productController->storePrintPrice($newRequest);

            DB::commit();
            return ResponseFormatter::success(
                [
                    'purchaseDetail' => $purchaseDetail,
                ],
                'Detail pembelian berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Gagal menambahkan detail pembelian',
                    'error' => $e->getMessage()
                ],
                'Gagal menambahkan detail pembelian',
                500
            );
        }
    }

    public function editDetail(PurchaseDetail $purchaseDetail)
    {
        return ResponseFormatter::success(
            ['purchaseDetail' => $purchaseDetail],
            'Data detail pembelian berhasil diambil'
        );
    }

    public function updateDetail(PurchaseDetail $purchaseDetail, Request $request)
    {
        $rules = [
            'costOfGoodSold' => 'required|numeric',
            'qty' => 'required|numeric',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()->first()
                ],
                'Validasi gagal',
                422
            );
        }

        try {
            DB::beginTransaction();
            $product = Product::find($purchaseDetail->product_id);

            // update data barang
            $product->expDate = $purchaseDetail->exp_date_old;
            $product->hargaPokok = $request->costOfGoodSold;
            $product->stok -= $purchaseDetail->quantity;
            if ($product->stok <= 0) {
                $product->stok = 0;
            }
            $product->save();

            // update data pembelian
            $purchase = Purchase::find($purchaseDetail->purchase_id);
            $purchase->amount -= $purchaseDetail->sub_amount;
            $purchase->total -= $purchaseDetail->quantity;
            $purchase->save();

            // update data detail pembelian
            $purchaseDetail->quantity = $request->qty;
            $purchaseDetail->exp_date = $request->expDate;
            $purchaseDetail->cost_of_good_sold = $request->costOfGoodSold;
            $purchaseDetail->sub_amount = $request->costOfGoodSold * $request->qty;
            $purchaseDetail->save();

            // update data barang
            $this->updateProductForDetailPurchase($product, $purchase, $purchaseDetail, $request);


            // update data pada cetak harga
            $productController = new ProductController();
            $printPrince = Barcode::where('IdBarang', $purchaseDetail->product_id)->first();
            if ($printPrince == null) {
                $newRequest = new Request([
                    'IdBarang' => $product->IdBarang,
                ]);
                $productController->storePrintPrice($newRequest);
            }

            DB::commit();
            return ResponseFormatter::success(
                [
                    'purchaseDetail' => $purchaseDetail,
                ],
                'Detail pembelian berhasil diubah'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Gagal mengubah detail pembelian',
                    'error' => $e->getMessage()
                ],
                'Gagal mengubah detail pembelian',
                500
            );
        }
    }

    public function destroyDetail(PurchaseDetail $purchaseDetail)
    {
        try {
            DB::beginTransaction();
            // update data barang
            $product = Product::find($purchaseDetail->product_id);
            $product->expDate = $purchaseDetail->exp_date_old;
            $product->hargaPokok = $purchaseDetail->cost_of_good_sold_old;
            $product->stok -= $purchaseDetail->quantity;
            if ($product->stok <= 0) {
                $product->stok = 0;
            }
            $product->save();

            // update data pembelian
            $purchase = Purchase::find($purchaseDetail->purchase_id);
            $purchase->amount -= $purchaseDetail->sub_amount;
            $purchase->total -= $purchaseDetail->quantity;
            $purchase->save();

            // hapus data pada cetak harga
            $productController = new ProductController();
            $printPrince = Barcode::where('IdBarang', $purchaseDetail->product_id)->first();
            if ($printPrince != null) {
                $productController->destroyPrintPrice($printPrince->ID);
            }

            // hapus detail pembelian
            $purchaseDetail->delete();
            DB::commit();
            return ResponseFormatter::success(
                null,
                'Pembelian berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'message' => 'Gagal menghapus pembelian',
                    'error' => $e->getMessage()
                ],
                'Gagal menghapus pembelian',
                500
            );
        }
    }
}
