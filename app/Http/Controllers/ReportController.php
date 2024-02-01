<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Jenis;
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
        $categories = Jenis::get();
        return ResponseFormatter::success(
            [
                'categories' => $categories
            ],
            'Data kategori berhasil diambil'
        );
    }
}
