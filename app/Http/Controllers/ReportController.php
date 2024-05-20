<?php

namespace App\Http\Controllers;

use App\Helpers\FilterRequest;
use App\Helpers\FormatDate;
use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Category;
use App\Models\Kasir;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
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
            'currentNav' => 'reportSale',
        ];
        return view('report.financial', $data);
    }

    /**
     * Mendapatkan data laporan penjualan
     */
    public function getReportSale(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
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

        $transactionsByYear = Kasir::selectRaw('DATE_FORMAT(tanggal, "%m-%Y") as month, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->when(true, function ($query) use ($filterDate) {
                $startOfTwoYearBefore = Carbon::parse($filterDate['date']->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfTwoYearBefore, $filterDate['date']->endOfMonth()]);
            })
            ->groupBy('month')
            ->orderByRaw('MIN(tanggal)')
            ->get();

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
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

        $reportPurchase = Purchase::selectRaw('sum(total) as total_purchase_product, sum(amount) as purchase')
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('created_at', $filterDate['date']->month)
                    ->whereYear('created_at', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('created_at', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->first();


        return ResponseFormatter::success(
            [
                'typeReport' => $filterDate['typeReport'],
                'dateString' => $filterDate['typeReport'] == 'Bulanan' ? FormatDate::month($filterDate['date']->month) : $filterDate['startDate']->copy()->format('d M Y') . ' - ' . $filterDate['endDate']->copy()->format('d M Y'),
                'date' => $filterDate['typeReport'] == 'Bulanan' ? $filterDate['date']->format('Y-m') : $filterDate['daterange'],
                'report' => $report,
                'reportPurchase' => $reportPurchase,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByYear' => $transactionsByYear,
            ],
            'Data laporan berhasil diambil'
        );
    }

    public function getSaleReportByTransactionDate(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $transactionsByDate = Kasir::selectRaw('tanggal, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
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

        return ResponseFormatter::success(
            [
                'transactionsByDate' => $transactionsByDate,
            ],
            'Data laporan transaksi penjualan berdasarkan tanggal berhasil diambil'
        );
    }

    public function getBestSellingProduct(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $bestSellingProducts = Kasir::selectRaw('nmBarang as name, sum(jumlah) as total, idBarang as id')
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->groupBy('idBarang', 'name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return ResponseFormatter::success(
            [
                'bestSellingProducts' => $bestSellingProducts,
            ],
            'Data produk terlaris berhasil diambil'
        );
    }

    public function getBestSellingCategory(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $bestSellingCategories = Category::selectRaw('p_jenis.ID as id, p_jenis.jenis as name, sum(t_kasir.jumlah) as total')
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
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return ResponseFormatter::success(
            [
                'bestSellingCategories' => $bestSellingCategories,
            ],
            'Data kategori terlaris berhasil diambil'
        );
    }

    public function getSaleReportTransactionByNoTransaction(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);

        $transactionByNoTransactions = Kasir::selectRaw('noTransaksi as no_transaction, max(tanggal) as date, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
                return $query->whereMonth('tanggal', $filterDate['date']->month)
                    ->whereYear('tanggal', $filterDate['date']->year);
            })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->orderBy('date', 'desc')
            ->groupBy('no_transaction')
            ->limit(100)
            ->get();

        return ResponseFormatter::success(
            [
                'transactionByNoTransactions' => $transactionByNoTransactions,
            ],
            'Data transaksi berdasarkan nomer transaksi berhasil diambil'
        );
    }

    public function getReportSaleByCategory(Request $request)
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

        $categories = Category::get(['jenis']);
        $categoryByDate = collect([]);
        $categoryByYear = collect([]);
        foreach ($categories as $category) {
            $categoryByDate[$category->jenis] = Kasir::selectRaw('tanggal, jenis, COALESCE(sum(total), 0) as income')
                ->leftJoin('t_barang', function ($join) {
                    $join->on('t_kasir.idBarang', '=', 't_barang.idBarang');
                })
                ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                    $query->whereMonth('tanggal', $date->month)
                        ->whereYear('tanggal', $date->year);
                })
                ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                })
                ->where('t_barang.jenis', $category->jenis)
                ->groupBy('tanggal', 'jenis')
                ->orderBy('tanggal', 'asc')
                ->get();

            $startDateCopy = $startDate->copy();
            while ($startDateCopy->lte($endDate)) {
                if (!$categoryByDate[$category->jenis]->contains('tanggal', $startDateCopy->format('Y-m-d'))) {
                    $categoryByDate[$category->jenis]->push([
                        'tanggal' => $startDateCopy->format('Y-m-d'),
                        'income' => 0,
                        'profit' => 0,
                        'total_product' => 0,
                        'jenis' => $category->jenis,
                    ]);
                }

                $startDateCopy->addDay();
            }
            $categoryByDate[$category->jenis] = $categoryByDate[$category->jenis]->sortBy('tanggal')->values()->all();


            // By Year
            $startOfTwoYearBefore = Carbon::parse($date->copy()->subYears(2))->startOfYear();
            $categoryByYear[$category->jenis] = Kasir::selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as month, jenis, COALESCE(sum(total), 0) as income')
                ->leftJoin('t_barang', function ($join) {
                    $join->on('t_kasir.idBarang', '=', 't_barang.idBarang');
                })
                ->when(true, function ($query) use ($date, $startOfTwoYearBefore) {
                    return $query->whereBetween('tanggal', [$startOfTwoYearBefore, $date->endOfMonth()]);
                })
                ->where('t_barang.jenis', $category->jenis)
                ->groupBy(DB::raw('DATE_FORMAT(tanggal, "%Y-%m")'), 'jenis')
                ->orderBy('month', 'asc')
                ->get();

            while ($startOfTwoYearBefore->lte($date)) {
                if (!$categoryByYear[$category->jenis]->contains('month', $startOfTwoYearBefore->format('Y-m'))) {
                    $categoryByYear[$category->jenis]->push([
                        'month' => $startOfTwoYearBefore->format('Y-m'),
                        'income' => 0,
                        'profit' => 0,
                        'total_product' => 0,
                        'jenis' => $category->jenis,
                    ]);
                }

                $startOfTwoYearBefore->addMonth();
            }
            $categoryByYear[$category->jenis] = $categoryByYear[$category->jenis]->sortBy('month')->values()->all();
        }

        return ResponseFormatter::success(
            [
                'typeReport' => $typeReport,
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'categoryByDate' => $categoryByDate,
                'categoryByYear' => $categoryByYear,
                'categories' => $categories
            ],
            'Data kategori berhasil diambil'
        );
    }

    public function ReportSaleDetail(Request $request)
    {
        $title = 'Toko Rian | Detail Penjualan';
        $sale = Kasir::where('noTransaksi', $request->id)->first();

        $data = [
            'title' => $title,
            'sale' => $sale,
            'currentNav' => 'reportSale',
        ];

        return view('report.saleDetail', $data);
    }

    public function getReportSaleDetail(Request $request)
    {
        $transactions = Kasir::where('noTransaksi', $request->id)->get();

        $report = Kasir::selectRaw('sum(total) as income, sum(laba) as profit, COUNT(DISTINCT noTransaksi) as total_transaction, sum(jumlah) as total_product')
            ->where('noTransaksi', $request->id)
            ->first();

        return ResponseFormatter::success(
            [
                'transactions' => $transactions,
                'report' => $report,
            ],
            'Data penjualan berhasil diambil'
        );
    }
}
