<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownloadDataController extends Controller
{
    public function shopping()
    {
        try {
            $shoppings = DB::table('t_belanja')->get();

            return ResponseFormatter::success([
                'shoppings' => $shoppings
            ], "Berhasil upload data belanja");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload data belanja");
        }
    }
}
