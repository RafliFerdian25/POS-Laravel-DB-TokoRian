<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function merk(Request $request)
    {
        try {
            $merks = $request->merks;
            foreach ($merks as $data) {
                DB::table('p_merk')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'merk' => $data['merk'],
                        'keterangan' => $data['keterangan'] == '' ? NULL : $data['keterangan']
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data merk");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload merk");
        }
    }

    public function category(Request $request)
    {
        try {
            $categories = $request->categories;
            foreach ($categories as $data) {
                DB::table('p_jenis')->updateOrInsert(
                    ['ID' => $data['ID']],
                    [
                        'jenis' => $data['jenis'],
                        'keterangan' => $data['keterangan'] == '' ? NULL : $data['keterangan']
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data kategori");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload kategori");
        }
    }

    public function unit(Request $request)
    {
        try {
            $units = $request->units;
            foreach ($units as $data) {
                DB::table('p_satuan')->updateOrInsert(
                    ['ID' => $data['ID']],
                    [
                        'satuan' => $data['satuan'],
                        'keterangan' => $data['keterangan'] == '' ? NULL : $data['keterangan']
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data satuan");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload satuan");
        }
    }

    public function product(Request $request)
    {
        try {
            $products = $request->products;

            foreach ($products as $data) {
                DB::table('t_barang')->updateOrInsert(
                    ['IdBarang' => $data['IdBarang']],
                    [
                        'nmBarang' => $data['nmBarang'],
                        'jenis' => $data['jenis'],
                        'satuan' => $data['satuan'],
                        'isi' => $data['isi'],
                        'hargaPokok' => $data['hargaPokok'],
                        'hargaJual' => $data['hargaJual'],
                        'hargaGrosir' => $data['hargaGrosir'],
                        'expDate' => $data['expDate'] == '0000-00-00' ? NULL : $data['expDate'],
                        'Rak' => $data['Rak'],
                        'stok' => $data['stok'],
                        'merk_id' => $data['merk_id'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data barang");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload barang");
        }
    }

    public function searchProduct(Request $request)
    {
        try {
            $searchProducts = $request->searchProducts;
            DB::table('t_barang_dicari')->truncate();

            foreach ($searchProducts as $data) {
                DB::table('t_barang_dicari')->insert(
                    [
                        'id' => $data['id'],
                        'product_id' => $data['product_id'],
                        'created_at' => $data['created_at'],
                        'updated_at' => $data['updated_at'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data barang dicari");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload barang dicari");
        }
    }

    public function shopping(Request $request)
    {
        try {
            $shopping = $request->shopping;
            DB::table('t_belanja')->truncate();

            foreach ($shopping as $data) {
                DB::table('t_belanja')->insert(
                    [
                        'id' => $data['id'],
                        'IdBarang' => $data['IdBarang'],
                        'nmBarang' => $data['nmBarang'],
                        'satuan' => $data['satuan'],
                        'jumlah' => $data['jumlah'],
                        'hargaPokok' => $data['hargaPokok'],
                        'TOTAL' => $data['TOTAL'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data belanja");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data belanja");
        }
    }

    public function sale(Request $request)
    {
        try {
            $sale = $request->sale;

            foreach ($sale as $data) {
                DB::table('t_kasir')->updateOrInsert(
                    ['ID' => $data['ID']],
                    [
                        'noUrut' => $data['noUrut'],
                        'noTransaksi' => $data['noTransaksi'],
                        'tanggal' => $data['tanggal'],
                        'idBarang' => $data['idBarang'],
                        'nmBarang' => $data['nmBarang'],
                        'jumlah' => $data['jumlah'],
                        'satuan' => $data['satuan'],
                        'harga' => $data['harga'],
                        'total' => $data['total'],
                        'Laba' => $data['Laba'],
                        'Bayar' => $data['Bayar'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data penjualan");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data penjualan");
        }
    }

    public function purchase(Request $request)
    {
        try {
            $purchase = $request->purchase;

            foreach ($purchase as $data) {
                DB::table('t_pembelian')->updateOrInsert(
                    ['id' => $data['id']],
                    [
                        'supplier_id' => $data['supplier_id'],
                        'total' => $data['total'],
                        'amount' => $data['amount'],
                        'created_at' => $data['created_at'],
                        'updated_at' => $data['updated_at'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data pembelian");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data pembelian");
        }
    }

    public function purchaseDetail(Request $request)
    {
        try {
            $purchaseDetail = $request->purchaseDetail;
            DB::table('t_pembelian_detail')->truncate();

            foreach ($purchaseDetail as $data) {
                DB::table('t_pembelian_detail')->insert(
                    [
                        'id' => $data['id'],
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity'],
                        'exp_date' => $data['exp_date'],
                        'exp_date_old' => $data['exp_date_old'],
                        'cost_of_good_sold' => $data['cost_of_good_sold'],
                        'cost_of_good_sold_old' => $data['cost_of_good_sold_old'],
                        'purchase_id' => $data['purchase_id'],
                        'sub_amount' => $data['sub_amount'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data detail pembelian");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data detail pembelian");
        }
    }

    public function receivable(Request $request)
    {
        try {
            $receivables = $request->receivables;
            DB::table('t_piutang')->truncate();

            foreach ($receivables as $data) {
                DB::table('t_piutang')->insert(
                    [
                        'noTransaksi' => $data['noTransaksi'],
                        'tanggal' => $data['tanggal'],
                        'nama_utang' => $data['nama_utang'],
                        'JUMLAH' => $data['JUMLAH'],
                        'sts_bayar' => $data['sts_bayar'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data piutang");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data piutang");
        }
    }

    public function supplier(Request $request)
    {
        try {
            $suppliers = $request->suppliers;

            foreach ($suppliers as $data) {
                DB::table('t_supplier')->updateOrInsert(
                    ['IdSupplier' => $data['IdSupplier']],
                    [
                        'Nama' => $data['Nama'],
                        'Produk' => $data['Produk'],
                        'alamat' => $data['alamat'] == '' ? '' : $data['alamat'],
                        'kota' => $data['kota'] == '' ? '' : $data['kota'],
                        'telp' => $data['telp'] == '' ? NULL : $data['telp'],
                        'email' => $data['email'] == '' ? NULL : $data['email'],
                    ],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data supplier");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data supplier");
        }
    }
}
