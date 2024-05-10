<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\BoxOpen;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BoxOpenController extends Controller
{
    const retail = ['PCS', 'BTL', 'SCT', 'CUP'];

    public function getProductBoxOpen(Product $product): JsonResponse
    {
        if (in_array($product->satuan, self::retail)) {
            $productBoxOpen = $product->productRetailOpen->load('productBox', 'productRetail');
        } else {
            $productBoxOpen = $product->productBoxOpen->load('productBox', 'productRetail');
        }

        return ResponseFormatter::success([
            'productBoxOpen' => $productBoxOpen,
        ], "Data produk berhasil diambil");
    }

    public function create(Product $product): View
    {
        $data = [
            'title' => 'POS TOKO | Barang',
            'productBox' => $product,
            'currentNav' => 'product',
        ];

        return view('product.createProductBoxOpen', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'idProductBox' => 'required|exists:t_barang,IdBarang',
            'idProductRetail' => 'required|exists:t_barang,IdBarang',
            'costOfGoodSoldRetail' => 'required|numeric',
            'content' => 'required|numeric',
        ];

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return ResponseFormatter::error([
                'message' => $validate->errors()->first(),
            ], "Data gagal divalidasi", 400);
        }

        $productBoxOpen = Product::find($request->idProductBox);
        $productRetailOpen = Product::find($request->idProductRetail);

        if (!$productBoxOpen || !$productRetailOpen) {
            return ResponseFormatter::error([
                'message' => 'Produk tidak ditemukan',
            ], "Data produk gagal diambil", 404);
        }

        try {
            DB::beginTransaction();
            // mengubah data stok produk
            $productBoxOpen->update([
                'stok' => $productBoxOpen->stok - 1,
            ]);

            $productRetailOpen->update([
                'stok' => $productRetailOpen->stok + $request->content,
                'hargaPokok' => ($request->costOfGoodSoldRetail > $productRetailOpen->harga_pokok || $productRetailOpen->stok <= 0) ? $request->costOfGoodSoldRetail : $productRetailOpen->harga_pokok,
            ]);

            // menyimpan data box open ke dalam database
            BoxOpen::create([
                'dus_id' => $request->idProductBox,
                'retail_id' => $request->idProductRetail,
                'harga_pokok_retail' => $request->costOfGoodSoldRetail,
            ]);
            DB::commit();
            return ResponseFormatter::success([
                'redirect' => route('barang.index'),
            ], "Data produk berhasil disimpan");
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], "Data produk gagal disimpan", 500);
        }
    }

    public function destroy(BoxOpen $boxOpen): JsonResponse
    {
        try {
            DB::beginTransaction();
            $productBoxOpen = Product::find($boxOpen->dus_id);
            $productRetailOpen = Product::find($boxOpen->retail_id);

            // mengubah data stok produk
            $productBoxOpen->update([
                'stok' => $productBoxOpen->stok + 1,
            ]);

            $productRetailOpen->update([
                'stok' => $productRetailOpen->stok - $productBoxOpen->isi,
                // 'hargaPokok' => $boxOpen->harga_pokok_retail_lama,
            ]);

            $boxOpen->delete();
            DB::commit();
            return ResponseFormatter::success([
                'redirect' => route('barang.index'),
            ], "Data produk berhasil dihapus");
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => $e->getMessage(),
            ], "Data produk gagal dihapus", 500);
        }
    }
}
