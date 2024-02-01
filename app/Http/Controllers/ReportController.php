<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Kasir;
use Illuminate\Http\Request;

class ReportController extends Controller
{
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
        $reports = Jenis::leftJoin('t_barang', function ($join) {
            $join->on('p_jenis.jenis', '=', 't_barang.jenis')
                ->leftJoin('t_kasir', 't_kasir.idBarang', '=', 't_barang.idBarang')
                ->whereYear('t_kasir.tanggal', 2019)
                ->whereMonth('t_kasir.tanggal', 6);
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
}
