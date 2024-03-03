<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function merk(Request $request)
    {
        try {
            $merks = $request->merks;
            foreach ($merks as $data) {
                DB::table('p_merk')->updateOrInsert(
                    ['id' => $data['id']],
                    ['merk' => $data['merk'], 'keterangan' => $data['keterangan']],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data merk");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload merk");
        }
    }

    public function category(Request $request)
    {
        try {
            $categories = $request->category;
            foreach ($categories as $data) {
                DB::table('p_jenis')->updateOrInsert(
                    ['ID' => $data->ID],
                    ['jenis' => $data->jenis, 'keterangan' => $data->keterangan],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data kategori");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload kategori");
        }
    }

    public function units(Request $request)
    {
        try {
            $units = $request->units;
            foreach ($units as $data) {
                DB::connection('hosting')->table('p_satuan')->updateOrInsert(
                    ['ID' => $data->ID],
                    ['satuan' => $data->satuan, 'keterangan' => $data->keterangan],
                );
            }

            return ResponseFormatter::success(null, "Berhasil upload data satuan");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan saat upload satuan");
        }
    }
}
