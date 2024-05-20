<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Toko;
use App\Models\Kasir;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\FormatDate;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductReportController extends Controller
{

    /**
     * Melihat laporan penjualan produk 
     */
    public function productReport()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan Barang',
            'typeReport' => 'Bulanan',
            'categories' => Category::all(),
            'currentNav' => 'reportProduct',
        ];
        return view('report.product', $data);
    }

    /**
     * Mendapatkan data laporan penjualan produk
     */
    public function getProductReport(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $startDate = Carbon::parse($request->month)->startOfMonth();
            $endDate = Carbon::now()->format('Y-m') == $request->month ? Carbon::now() : Carbon::parse($request->month)->endOfMonth();
            $typeReport = "Bulanan";
        }

        $query = Product::select('t_barang.IdBarang', 't_barang.nmBarang', 'expDate', 'stok', DB::raw('COALESCE(SUM(t_kasir.jumlah), 0) as sold'), DB::raw('COALESCE(SUM(t_kasir.total), 0) as income'), DB::raw('COALESCE(SUM(t_kasir.laba), 0) as profit'))
            ->leftJoin('t_kasir', function ($join) use ($typeReport, $date, $startDate, $endDate) {
                $join->on('t_kasir.idBarang', '=', 't_barang.IdBarang')
                    ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                        return $query->whereMonth('tanggal', $date->month)
                            ->whereYear('tanggal', $date->year);
                    })
                    ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('tanggal', [$startDate, $endDate]);
                    });
            })
            ->when($request->filterProduct != null, function ($query) use ($request) {
                return $query->where('t_barang.nmBarang', 'LIKE', '%' . $request->input('filterProduct') . '%')
                    ->orWhere('t_barang.idBarang', 'LIKE', '%' . $request->input('filterProduct') . '%');
            })
            ->when($request->filterCategory != null, function ($query) use ($request) {
                return $query->where('t_barang.jenis', $request->input('filterCategory'));
            })
            ->when($request->filterMerk != null, function ($query) use ($request) {
                return $query->where('merk_id', $request->input('filterMerk'));
            });

        $countProduct = $query->groupBy('t_barang.IdBarang', 't_barang.nmBarang', 'expDate', 'stok')
            ->havingRaw('SUM(t_kasir.jumlah) > 0')
            ->count();
        $products = $query->groupBy('t_barang.IdBarang', 't_barang.nmBarang', 'expDate', 'stok')
            ->orderByDesc('sold')
            ->limit(100)
            ->get();

        return ResponseFormatter::success(
            [
                'products' => $products,
                'countProduct' => $countProduct,
                'typeReport' => $typeReport,
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
            ],
            'Data berhasil diambil'
        );
    }

    public function productDetail(Product $product)
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan Barang',
            'product' => $product->load(['productHasExpiredBefore' => function ($subquery) {
                $subquery->select('id', 'product_id', 'quantity', 'expired_date')
                    ->where('quantity', '>', 0)
                    ->first(); // Batasi agar hanya mengambil satu hasil
            }]),
            'typeReport' => 'Bulanan',
            'currentNav' => 'reportProduct',
        ];

        return view('report.productDetail', $data);
    }

    public function getProductDetail(Request $request, Product $product)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($product) {
                $query->where('IdBarang', $product->IdBarang);
            })
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->first();

        // Jika report tidak kosong, menghitung total transaksi
        if (!$report->isEmpty) {
            $report->total_transaction = (int)$report->total_transaction;
        } else {
            $report->total_transaction = 0;
        }

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($product) {
                $query->where('IdBarang', $product->IdBarang);
            })
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $transactionsByLastYear = Kasir::selectRaw('max(tanggal) as month, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($product) {
                $query->where('IdBarang', $product->IdBarang);
            })
            ->when(true, function ($query) use ($date) {
                $startOfYear = Carbon::parse($date->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfYear, $date->endOfMonth()]);
            })
            ->groupBy(DB::raw('month(tanggal)'), DB::raw('year(tanggal)'))
            ->orderBy('month', 'asc')
            ->get();

        $transactionsByNoTransaction = Kasir::selectRaw('tanggal as dateTransaction, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($product) {
                $query->where('IdBarang', $product->IdBarang);
            })
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('dateTransaction')
            ->orderBy('dateTransaction', 'desc')
            ->get();

        $purchaseDetails = PurchaseDetail::with('purchase:id,supplier_id,created_at', 'purchase.supplier:IdSupplier,Nama')
            ->where('product_id', $product->IdBarang)
            ->orderBy('id', 'desc')
            ->get();

        return ResponseFormatter::success(
            [
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'product' => $product,
                'report' => $report,
                'typeReport' => $typeReport,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByNoTransaction' => $transactionsByNoTransaction,
                'transactionsByLastYear' => $transactionsByLastYear,
                'purchaseDetails' => $purchaseDetails
            ],
            'Data kategori berhasil diambil'
        );
    }
}
