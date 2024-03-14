<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Category;
use App\Models\Merk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function data()
    {
        $categories = Category::withCount('products')->get();
        return ResponseFormatter::success([
            'categories' => $categories
        ], 'Kategori berhasil diambil');
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
    public function edit(Category $category)
    {
        // menyeleksi data kategori berdasarkan id yang dipilih
        return ResponseFormatter::success([
            'category' => $category
        ], 'Kategori berhasil diambil');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // menyeleksi data yang akan diinputkan
        $rules = [
            'id' => 'required|unique:p_jenis,id,' . $category->ID . ',ID',
            'name' => 'required',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                'message' => $validated->errors()->first()
            ], 'Gagal mengupdate kategori', 422);
        }

        // mengubah data kategori 
        $category->update([
            'ID' => strtoupper($request->id),
            'jenis' => strtoupper($request->id),
            'keterangan' => $request->name,
        ]);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return ResponseFormatter::success(null, 'Kategori berhasil diubah');
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
