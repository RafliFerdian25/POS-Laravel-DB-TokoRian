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
        $title = 'POS TOKO | Kategori';
        $merks = Merk::all();
        return view('merk.index', compact('title', 'merks'));
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
    public function edit($id)
    {
        $merk = Merk::findOrFail($id);
        $title = 'POS TOKO | Kategori';
        return view('merk.update', compact('merk', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate(['name' => 'required']);
        Merk::where('id', $id)->update($validated);
        return redirect()->route('kategori.index')->with('success', 'Berhasil mengubah merk.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Merk::destroy($id);
        return redirect()->route('kategori.index')->with('success', 'Berhasil menghapus merk.');
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
