<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Merk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MerkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Merk';
        return view('merk.index', compact('title'));
    }

    public function indexData()
    {
        $merks = Merk::withCount('products')->get();
        return ResponseFormatter::success([
            'merks' => $merks,
        ], 'Data berhasil diambil.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Kategori';
        return view('merk.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // menyeleksi data yang akan diinputkan
        $rules = [
            'name' => 'required|unique:p_merk,merk',
            'description' => 'required'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'error' => $validated->errors()->first()
            ], 'Data tidak valid.', 422);
        }

        try {
            // menginput data ke table products
            DB::beginTransaction();
            Merk::create([
                'merk' => $request->name,
                'keterangan' => $request->description
            ]);
            DB::commit();

            return ResponseFormatter::success(
                [
                    'redirect' => route('merk.index')
                ],
                'Data merk berhasil ditambahkan.'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal ditambahkan.', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Merk $merk)
    {
        return ResponseFormatter::success([
            'merk' => $merk
        ], 'Data berhasil diambil.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Merk $merk)
    {
        $rules = [
            'name' => 'required|unique:p_merk,merk,' . $merk->id,
            'description' => 'required'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'error' => $validated->errors()->first()
            ], 'Data tidak valid.', 422);
        }

        try {
            DB::beginTransaction();
            $merk->update([
                'merk' => $request->name,
                'keterangan' => $request->description
            ]);
            DB::commit();

            return ResponseFormatter::success(
                null,
                'Data merk berhasil diubah.'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal diubah.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Merk $merk)
    {
        try {
            DB::beginTransaction();
            $merk->delete();
            DB::commit();
            return ResponseFormatter::success(
                null,
                'Data merk berhasil dihapus.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data gagal dihapus.', 500);
        }
    }

    /**
     * Search data from storage.
     */
    public function searchData(Request $request)
    {
        $merks = Merk::where('merk', 'like', '%' . $request->q . '%')->get();

        return ResponseFormatter::success([
            'merks' => $merks
        ], 'Data berhasil diambil');
    }
}
