<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Kasir;
use App\Models\Setting;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $penjualan = Sale::findOrFail($request->id_penjualan);
        $penjualan->total_item = $request->total_item;
        $penjualan->total_price = $request->total;
        $penjualan->pay = $request->dibayar;
        $penjualan->change = $request->dibayar - $request->total;
        $penjualan->discount = $request->diskon;
        $penjualan->status = true;

        $detail = SaleDetail::where('sale_id', $penjualan->id)->get();
        foreach ($detail as $item) {
            // $item->discount = $request->diskon;
            // $item->update();

            $produk = Product::find($item->product_id);
            $produk->stock -= $item->qty;
            $produk->update();

            $profit = ($item->selling_price - $item->purchase_price) * $item->qty;
            $penjualan->profit += $profit - ($item->discount * $item->qty);
            $penjualan->discount += $item->discount;
        }
        $penjualan->profit -= $request->diskon;
        $penjualan->update();

        return redirect()->route('transaksi.selesai');
    }

    public function selesai()
    {
        $title = 'POS TOKO | Nota';
        $setting = Setting::first();

        return view('sale.selesai', compact('setting', 'title'));
    }

    public function notaKecil()
    {
        $title = 'POS TOKO | Nota';
        $setting = Setting::first();
        // dd($setting);
        $penjualan = Sale::find(session('id_penjualan'));
        if (!$penjualan) {
            abort(404);
        }
        $detail = SaleDetail::with('product')
            ->where('sale_id', session('id_penjualan'))
            ->get();

        return view('sale.nota_kecil', compact('setting', 'penjualan', 'detail', 'title'));
    }

    public function laporanBulanan(Request $request)
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
        $kasir = Kasir::selectRaw('noTransaksi, max(tanggal) as tanggal, sum(total) as total, sum(laba) as laba, sum(jumlah) as jumlah')
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
            'kasir' => $kasir,
            'title' => $title,
            'report' => $report,
            'tanggal' => $tanggal,
            'barangTerlaris' => $barangTerlaris,
            'jenisTerlaris' => $jenisTerlaris,
        ];
        return view('report.financial', $data);
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
        return view('report.product', $data);
    }

    public function showReport($id)
    {
        $title = 'POS TOKO | Laporan';
        $setting = Toko::first();
        $sales = Kasir::join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('sales.id', 'sales.created_at', 'sale_details.*', 'products.name as product_name', 'products.purchase_price as purchase_price', 'products.selling_price as selling_price')
            ->where("sales.id", $id)
            ->get();
        // dd($sales);
        return view('report.show', compact('setting', 'sales', 'title'));
    }
}
