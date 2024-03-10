<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Category;
use App\Models\Merk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Kategori';
        $categories = Category::withCount('products')->get();
        $data = [
            'categories' => $categories,
            'title' => $title,
            'currentNav' => 'category',
        ];

        return view('category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Kategori';
        $data = [
            'title' => $title,
            'currentNav' => 'category',
        ];

        return view('category.create', $data);
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
            'id' => 'required|unique:p_jenis',
            'name' => 'required',
        ]);

        // menginput data ke table products
        Category::create([
            'ID' => strtoupper($validated['id']),
            'jenis' => strtoupper($validated['id']),
            'keterangan' => $validated['name'],
        ]);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('category.index')->with('success', 'Berhasil menambahkan kategori.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // menyeleksi data product berdasarkan id yang dipilih
        $category = DB::table('categories')->find($id);
        $title = 'POS TOKO | Kategori';
        $data = [
            'category' => $category,
            'title' => $title,
            'currentNav' => 'category',
        ];

        return view('category.update', $data);
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
        // menyeleksi data yang akan diinputkan
        if ($request->id == $id) {
            $validated = $request->validate([
                'id' => 'required',
                'name' => 'required',
            ]);
        } else {
            $validated = $request->validate([
                'id' => 'required|unique:categories',
                'name' => 'required',
            ]);
        }
        $validated['id'] = strtoupper($validated['id']);
        // mengupdate data di table Categoriess
        Category::whereId($id)->update($validated);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('category.index')->with('success', 'Kategori berhasil diupdate');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::destroy($id);
        return ResponseFormatter::success(null, 'Kategori berhasil dihapus');
    }
}
