<?php

namespace App\Http\Controllers;

use App\Helpers\FilterRequest;
use App\Helpers\FormatDate;
use Carbon\Carbon;
use App\Models\Kasir;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;

class CategoryReportController extends Controller
{

    public function categoryIndex()
    {
        $data = [
            'title' => 'Laporan Kategori',
            'kategori' => Category::all(),
            'currentNav' => 'reportCategory',
            'typeReport' => 'Bulanan',
        ];

        return view('report.category', $data);
    }

    public function getCategoriesReport(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $reports = Category::leftJoin('t_barang', function ($join) use ($filterDate) {
            $join->on('p_jenis.jenis', '=', 't_barang.jenis')
                ->leftJoin('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
                ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                    return $query->whereMonth('tanggal', $filterDate['date']->month)
                        ->whereYear('tanggal', $filterDate['date']->year);
                })
                ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                    return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
                });
        })
            ->selectRaw('p_jenis.jenis, p_jenis.keterangan, COALESCE(SUM(t_kasir.jumlah), 0) as jumlah')
            ->groupBy('p_jenis.jenis', 'p_jenis.keterangan')
            ->get();

        $bestSellingCategories = Category::selectRaw('p_jenis.ID as id, p_jenis.keterangan as name, sum(t_kasir.jumlah) as total, sum(t_kasir.total) as income, sum(t_kasir.laba) as profit')
            ->join('t_barang', 't_barang.jenis', '=', 'p_jenis.jenis')
            ->join('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->groupBy('id', 'name')
            ->orderBy('profit', 'desc')
            ->get();

        return ResponseFormatter::success(
            [
                'reports' => $reports,
                'bestSellingCategories' => $bestSellingCategories,
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
            'title' => 'Toko Rian | Laporan Detail Kategori',
            'category' => $category,
            'typeReport' => 'Bulanan',
            'currentNav' => 'reportCategory',
        ];

        return view('report.categoryDetail', $data);
    }

    /**
     * Mendapatkan data laporan detail kategori
     */
    public function getCategoryDetail(Request $request, Category $category)
    {
        $filterDate = FilterRequest::filterDate($request);

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->first();

        // Jika report tidak kosong, menghitung total transaksi
        if (!$report->isEmpty) {
            $report->total_transaction = (int)$report->total_transaction;
        } else {
            $report->total_transaction = 0;
        }

        $bestSellingProducts = Kasir::with('product:IdBarang,stok')
            ->select('idBarang', 'nmBarang as name', DB::raw('COALESCE(SUM(t_kasir.jumlah), 0) as total'), DB::raw('COALESCE(SUM(t_kasir.total), 0) as income'), DB::raw('COALESCE(SUM(t_kasir.laba), 0) as profit'))
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->groupBy('nmBarang', 'idBarang')
            ->orderBy('total', 'desc')
            ->limit(50)
            ->get();

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $transactionsByLastYear = Kasir::selectRaw('max(tanggal) as month, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->whereHas('product', function ($query) use ($category) {
                $query->where('jenis', $category->jenis);
            })
            ->when(true, function ($query) use ($filterDate) {
                $startOfYear = Carbon::parse($filterDate['date']->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfYear, $filterDate['date']->endOfMonth()]);
            })
            ->groupBy(DB::raw('month(tanggal)'), DB::raw('year(tanggal)'))
            ->orderBy('month', 'asc')
            ->get();

        return ResponseFormatter::success(
            [
                'dateString' => $filterDate['typeReport'] == 'Bulanan' ? FormatDate::month($filterDate['date']->month) : $filterDate['startDate']->copy()->format('d M Y') . ' - ' . $filterDate['endDate']->copy()->format('d M Y'),
                'date' => $filterDate['typeReport'] == 'Bulanan' ? $filterDate['date']->format('Y-m') : $filterDate['daterange'],
                'category' => $category,
                'report' => $report,
                'typeReport' => $filterDate['typeReport'],
                'bestSellingProducts' => $bestSellingProducts,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByLastYear' => $transactionsByLastYear,
            ],
            'Data kategori berhasil diambil'
        );
    }
}
