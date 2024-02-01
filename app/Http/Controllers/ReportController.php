<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Kasir;
use Carbon\Carbon;
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
}
