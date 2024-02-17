<?php

namespace App\Http\Controllers;

use App\Helpers\FormatDate;
use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Category;
use App\Models\Kasir;
use App\Models\Product;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan penjualan bulanan
     */
    public function ReportSale()
    {
        $title = 'POS TOKO | Laporan';
        $setting = Toko::first();

        $data = [
            'setting' => $setting,
            'title' => $title,
            'typeReport' => 'Bulanan',
        ];
        return view('report.financial', $data);
    }

    public function getReportSale(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;
        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $date = Carbon::now();
            $daterange = explode(' - ', $request->daterange);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $transactionByNoTransactions = Kasir::selectRaw('noTransaksi as no_transaction, max(tanggal) as date, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->orderBy('no_transaction', 'desc')
            ->groupBy('no_transaction')
            ->get();

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
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

        $transactionsByYear = Kasir::selectRaw('max(tanggal) as month, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->when(true, function ($query) use ($date) {
                $startOfTwoYearBefore = Carbon::parse($date->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfTwoYearBefore, $date->endOfMonth()]);
            })
            ->groupBy(DB::raw('month(tanggal)'), DB::raw('year(tanggal)'))
            ->orderBy('month', 'asc')
            ->get();

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
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

        $bestSellingProducts = Kasir::selectRaw('nmBarang as name, sum(jumlah) as total, idBarang as id')
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('idBarang', 'name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $bestSellingCategories = Category::selectRaw('p_jenis.ID as id, p_jenis.jenis as name, sum(t_kasir.jumlah) as total')
            ->join('t_barang', 't_barang.jenis', '=', 'p_jenis.jenis')
            ->join('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('id', 'name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return ResponseFormatter::success(
            [
                'typeReport' => $typeReport,
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'report' => $report,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByYear' => $transactionsByYear,
                'transactionByNoTransactions' => $transactionByNoTransactions,
                'bestSellingProducts' => $bestSellingProducts,
                'bestSellingCategories' => $bestSellingCategories,
            ],
            'Data laporan berhasil diambil'
        );
    }
    public function categoryIndex()
    {
        $data = [
            'title' => 'Laporan Kategori',
            'kategori' => Category::all()
        ];

        return view('report.category', $data);
    }

    public function getCategoriesReport(Request $request)
    {
        $date = Carbon::createFromFormat('Y-m', $request->reportDate);

        $reports = Category::leftJoin('t_barang', function ($join) use ($date) {
            $join->on('p_jenis.jenis', '=', 't_barang.jenis')
                ->leftJoin('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
                ->whereYear('t_kasir.tanggal', $date->year)
                ->whereMonth('t_kasir.tanggal', $date->month);
        })
            ->selectRaw('p_jenis.jenis, p_jenis.keterangan, COALESCE(SUM(t_kasir.jumlah), 0) as jumlah')
            ->groupBy('p_jenis.jenis', 'p_jenis.keterangan')
            ->get();

        return ResponseFormatter::success(
            [
                'reports' => $reports
            ],
            'Data kategori berhasil diambil'
        );
    }

    /**
     * Laporan Detail Kategori 
     */
    public function categoryDetail(Category $category)
    {
        $data = [
            'title' => 'Laporan Kategori',
            'category' => $category,
            'typeReport' => 'Bulanan'
        ];

        return view('report.categoryDetail', $data);
    }

    /**
     * Mendapatkan data laporan detail kategori
     */
    public function getCategoryDetail(Request $request, Category $category)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $date = Carbon::now();
            $daterange = explode(' - ', $request->daterange);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
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

        $bestSellingProducts = Kasir::selectRaw('nmBarang as name, sum(jumlah) as total, idBarang')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('name', 'idBarang')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
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
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when(true, function ($query) use ($date) {
                $startOfYear = Carbon::parse($date->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfYear, $date->endOfMonth()]);
            })
            ->groupBy(DB::raw('month(tanggal)'), DB::raw('year(tanggal)'))
            ->orderBy('month', 'asc')
            ->get();

        $transactionsByNoTransaction = Kasir::selectRaw('noTransaksi, max(tanggal) as tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->groupBy('noTransaksi')
            ->orderBy('tanggal', 'asc')
            ->get();

        return ResponseFormatter::success(
            [
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'category' => $category,
                'report' => $report,
                'typeReport' => $typeReport,
                'bestSellingProducts' => $bestSellingProducts,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByNoTransaction' => $transactionsByNoTransaction,
                'transactionsByLastYear' => $transactionsByLastYear,
            ],
            'Data kategori berhasil diambil'
        );
    }

    /**
     * Melihat laporan penjualan produk 
     */
    public function monthlyProductReport()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan',
        ];
        return view('report.product', $data);
    }

    /**
     * Mendapatkan data laporan penjualan produk
     */
    public function monthlyProductReportData(Request $request)
    {
        $filterDate = $request->filterDate == null ? Carbon::now() : Carbon::parse($request->filterDate);
        $query = Product::select('t_barang.IdBarang', 't_barang.nmBarang', 'expDate', 'stok', DB::raw('COALESCE(SUM(t_kasir.jumlah), 0) as jumlah'))
            ->join('t_kasir', 't_barang.idBarang', '=', 't_kasir.idBarang')
            ->when($request->filterName != null, function ($query) use ($request) {
                return $query->where('t_barang.nmBarang', 'LIKE', '%' . $request->input('filterName') . '%');
            })
            ->when($request->filterBarcode != null, function ($query) use ($request) {
                return $query->where('t_barang.idBarang', 'LIKE', '%' . $request->input('filterBarcode') . '%');
            })
            ->whereMonth('t_kasir.tanggal', $filterDate->month)
            ->whereYear('t_kasir.tanggal', $filterDate->year)
            ->groupBy('t_barang.IdBarang', 't_barang.nmBarang', 'expDate', 'stok')
            ->orderByDesc('jumlah');

        $products = $query->get();
        $countProduct = $query->toBase()->getCountForPagination();

        return ResponseFormatter::success(
            [
                'products' => $products,
                'countProduct' => $countProduct
            ],
            'Data berhasil diambil'
        );
    }

    public function laporanBarangBulanan(Request $request, Product $product)
    {
        $product->load('type');
        $title = 'POS TOKO | Laporan';
        if ($request->laporan_bulan == null) {
            $tanggal = date('Y-m');
        } else {
            // $tanggal = explode("-",$request->laporan_bulan);
            $tanggal = date('Y-m', strtotime($request->laporan_bulan));
        }
        $tahun = Carbon::parse($tanggal)->format('Y');
        $bulan = Carbon::parse($tanggal)->format('m');

        $setting = Toko::first();

        $transactions = Kasir::selectRaw('tanggal, noTransaksi, jumlah, total, laba')
            ->where('idBarang', $product->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $report = Kasir::where('idBarang', $product->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('sum(total) as income, sum(laba) as profit, sum(jumlah) as total_item')
            ->first();

        $reportMonths = Kasir::selectRaw('MONTHNAME(tanggal) as month, sum(total) as income')
            ->where('idBarang', $product->IdBarang)
            ->whereYear('tanggal', $tahun)
            ->groupBy('month')
            ->orderBy('tanggal', 'asc')
            ->get();

        $reportDays = Kasir::selectRaw('DATE_FORMAT(tanggal, "%d/%m/%Y") as day, sum(total) as income')
            ->where('idBarang', $product->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $data = [
            'setting' => $setting,
            'title' => $title,
            'tanggal' => $tanggal,
            'barang' => $product,
            'transactions' => $transactions,
            'report' => $report,
            'reportMonths' => $reportMonths,
            'reportDays' => $reportDays,
        ];
        return view('report.detailProduct', $data);
    }
}
