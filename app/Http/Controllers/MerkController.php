<?php

namespace App\Http\Controllers;

use App\Models\Merk;
use Illuminate\Http\Request;

class MerkController extends Controller
{
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
        $validated = $request->validate([
            'name' => 'required',
        ]);

        // menginput data ke table products
        Merk::create($validated);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('kategori.index')->with('success', 'Berhasil menambahkan merk.');
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
}
