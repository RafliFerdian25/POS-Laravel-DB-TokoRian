<?php

namespace App\Http\Controllers;

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
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
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
            ->orderBy('date', 'desc')
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

        $transactionsByYear = Kasir::selectRaw('DATE_FORMAT(tanggal, "%m-%Y") as month, sum(total) as income, sum(laba) as profit, sum(jumlah) as total_product')
            ->when(true, function ($query) use ($date) {
                $startOfTwoYearBefore = Carbon::parse($date->copy()->subYears(2))->startOfYear();
                return $query->whereBetween('tanggal', [$startOfTwoYearBefore, $date->endOfMonth()]);
            })
            ->groupBy('month')
            ->orderByRaw('MIN(tanggal)')
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

        $reportPurchase = Purchase::selectRaw('sum(total) as total_purchase_product, sum(amount) as purchase')
            ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                return $query->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year);
            })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
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
                'reportPurchase' => $reportPurchase,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByYear' => $transactionsByYear,
                'transactionByNoTransactions' => $transactionByNoTransactions,
                'bestSellingProducts' => $bestSellingProducts,
                'bestSellingCategories' => $bestSellingCategories,
            ],
            'Data laporan berhasil diambil'
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

        $reports = Category::leftJoin('t_barang', function ($join) use ($date, $typeReport, $startDate, $endDate) {
            $join->on('p_jenis.jenis', '=', 't_barang.jenis')
                ->leftJoin('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
                ->when($typeReport == 'Bulanan', function ($query) use ($date) {
                    return $query->whereMonth('tanggal', $date->month)
                        ->whereYear('tanggal', $date->year);
                })
                ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                });
        })
            ->selectRaw('p_jenis.jenis, p_jenis.keterangan, COALESCE(SUM(t_kasir.jumlah), 0) as jumlah')
            ->groupBy('p_jenis.jenis', 'p_jenis.keterangan')
            ->get();

        $bestSellingCategories = Category::selectRaw('p_jenis.ID as id, p_jenis.keterangan as name, sum(t_kasir.jumlah) as total, sum(t_kasir.total) as income, sum(t_kasir.laba) as profit')
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

        $bestSellingProducts = Kasir::with('product:IdBarang,stok')
            ->select('idBarang', 'nmBarang as name', DB::raw('COALESCE(SUM(t_kasir.jumlah), 0) as total'), DB::raw('COALESCE(SUM(t_kasir.total), 0) as income'), DB::raw('COALESCE(SUM(t_kasir.laba), 0) as profit'))
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
            ->groupBy('nmBarang', 'idBarang')
            ->orderBy('total', 'desc')
            ->limit(50)
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

        return ResponseFormatter::success(
            [
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'category' => $category,
                'report' => $report,
                'typeReport' => $typeReport,
                'bestSellingProducts' => $bestSellingProducts,
                'transactionsByDate' => $transactionsByDate,
                'transactionsByLastYear' => $transactionsByLastYear,
            ],
            'Data kategori berhasil diambil'
        );
    }

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
            'product' => $product,
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
