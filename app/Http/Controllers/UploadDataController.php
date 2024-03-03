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
    public function upload(Request $request)
    {
        try {
            $merks = $request->merks;
            foreach ($merks as $data) {
                DB::table('p_merk')->updateOrInsert(
                    ['id' => $data['id']],
                    ['merk' => $data['merk'], 'keterangan' => $data['keterangan']],
                );
            }

            return ResponseFormatter::success(null, "Berhasil");
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], "Terjadi Kesalahan");
        }
    }
}
