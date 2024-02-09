<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Category;
use App\Models\Merk;
use App\Models\Product;
use App\Models\WholesalePurchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WholesalePurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Belanja';
        return view('purchase.purchase', compact('title'));
    }

    /**
     * Menampilkan data permintaan index.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData()
    {
        $wholesalePurchases = WholesalePurchase::select('id', 'IdBarang', 'nmBarang', 'satuan', 'hargaPokok', 'jumlah', 'total')
            ->orderBy('nmBarang', 'desc')
            ->get();

        return ResponseFormatter::success([
            'wholesalePurchases' => $wholesalePurchases
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
            'IdBarang' => 'required|exists:t_barang,IdBarang',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'error' => $validated->errors()->first()
            ], 'Data gagal divalidasi', 422);
        }

        try {
            DB::beginTransaction();
            $product = Product::where('IdBarang', $request->IdBarang)->first();

            $purchase = WholesalePurchase::create([
                'IdBarang' => $product->IdBarang,
                'nmBarang' => $product->nmBarang,
                'satuan' => $product->satuan,
                'hargaPokok' => $product->hargaPokok,
                'jumlah' => 2,
                'TOTAL' => $product->hargaPokok * 2,
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

    public function createWholesalePurchaseDetails($id)
    {
        $title = 'POS TOKO | Belanja';
        $purchaseId = $id;
        $categories = Category::get();
        $merks = Merk::get();
        $product = Product::orderBy('name')->get();
        return view('purchase.create-purchase-details', compact('purchaseId', 'title', 'categories', 'merks', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(WholesalePurchase $wholesalePurchase)
    {
        try {
            DB::beginTransaction();
            $wholesalePurchase->delete();
            DB::commit();
            return ResponseFormatter::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal dihapus', 500);
        }
    }
}
