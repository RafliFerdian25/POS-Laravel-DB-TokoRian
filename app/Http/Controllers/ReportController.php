<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Kasir;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan penjualan bulanan
     */
    public function ReportSaleMonthly(Request $request)
    {
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
        $transactions = Kasir::selectRaw('noTransaksi, max(tanggal) as tanggal, sum(total) as total, sum(laba) as laba, sum(jumlah) as jumlah')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('noTransaksi', 'desc')
            ->groupBy('noTransaksi')
            ->get();
        $report = Kasir::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('sum(total) as income, sum(laba) as profit, (select count(noUrut) from t_kasir where month(`tanggal`) = ' . $bulan . ' and year(`tanggal`) = ' . $tahun . ' and noUrut = 1) as total_transaction, sum(jumlah) as total_item')
            ->get();

        $barangTerlaris = Kasir::selectRaw('nmBarang as namaBarang, sum(jumlah) as total, idBarang')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('namaBarang', 'idBarang')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $jenisTerlaris = Jenis::selectRaw('p_jenis.jenis, sum(t_kasir.jumlah) as total')
            ->join('t_barang', 't_barang.jenis', '=', 'p_jenis.jenis')
            ->join('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
            ->whereMonth('t_kasir.tanggal', $bulan)
            ->whereYear('t_kasir.tanggal', $tahun)
            ->groupBy('jenis')
            ->orderBy('total', 'desc')
            ->get();

        $data = [
            'setting' => $setting,
            'transactions' => $transactions,
            'title' => $title,
            'report' => $report,
            'tanggal' => $tanggal,
            'barangTerlaris' => $barangTerlaris,
            'jenisTerlaris' => $jenisTerlaris,
        ];
        return view('report.financial', $data);
    }

    public function categoryIndex()
    {
        $data = [
            'title' => 'Laporan Kategori',
            'kategori' => Jenis::all()
        ];

        return view('report.category', $data);
    }

    public function getCategoriesReport(Request $request)
    {
        $date = Carbon::createFromFormat('Y-m', $request->reportDate);

        $reports = Jenis::leftJoin('t_barang', function ($join) use ($date) {
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

    public function laporanBarangBulanan(Request $request, Barang $barang)
    {
        $barang->load('type');
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
            ->where('idBarang', $barang->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $report = Kasir::where('idBarang', $barang->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('sum(total) as income, sum(laba) as profit, sum(jumlah) as total_item')
            ->first();

        $reportMonths = Kasir::selectRaw('MONTHNAME(tanggal) as month, sum(total) as income')
            ->where('idBarang', $barang->IdBarang)
            ->whereYear('tanggal', $tahun)
            ->groupBy('month')
            ->orderBy('tanggal', 'asc')
            ->get();

        $reportDays = Kasir::selectRaw('DATE_FORMAT(tanggal, "%d/%m/%Y") as day, sum(total) as income')
            ->where('idBarang', $barang->IdBarang)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $data = [
            'setting' => $setting,
            'title' => $title,
            'tanggal' => $tanggal,
            'barang' => $barang,
            'transactions' => $transactions,
            'report' => $report,
            'reportMonths' => $reportMonths,
            'reportDays' => $reportDays,
        ];
        return view('report.detailProduct', $data);
    }
}
