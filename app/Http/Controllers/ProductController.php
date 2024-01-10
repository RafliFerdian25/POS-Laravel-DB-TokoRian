<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Jenis;
use App\Models\Merk;
use App\Models\Product;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Barang';
        $products = Product::Select("id", "name", "unit", "purchase_price", "selling_price", "wholesale_price", "stock", "expired_date")->get();
        return view('product.product', compact('products', 'title'));
    }
    public function data()
    {
        $product = Product::Select("id", "name", "unit", "purchase_price", "selling_price", "wholesale_price", "stock", "expired_date")->get();
        $data = array();
        foreach ($product as $item) {
            $row = array();
            $row['id'] = $item->id;
            $row['name'] = $item->name;
            $row['unit'] = $item->unit;
            $row['purchase_price'] = $item->purchase_price;
            $row['selling_price'] = $item->selling_price;
            $row['wholesale_price'] = $item->wholesale_price;
            $row['stock'] = $item->stock;
            $row['expired_date'] = $item->expired_date;
            $row['action'] = '<a href="' . route('barang.edit', $item->id) . '" class="btn btn-link btn-lg float-left px-0" id="' . $item->id . '"><i class="fa fa-edit"></i></a>
                        <a href="#" onclick="deleteData(`' . route('barang.destroy', $item->id) . '`)" class="btn btn-link btn-lg float-right px-0 color__red1" id="' . $item->id . '"><i class="fa fa-trash"></i></a>';

            $data[] = $row;
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Barang';
        $categories = Category::get();
        $merks = Merk::orderBy('name')->get();
        return view('product.create', compact('categories',  'merks', 'title'));
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
            'id' => 'required|unique:products',
            'category_id' => 'required',
            'merk_id' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'contain' => 'required',
            'discount' => 'required|numeric|min:0|max:999999999',
            'purchase_price' => 'required|numeric|min:0|max:999999999',
            'selling_price' => 'required|numeric|min:0|max:999999999',
            'wholesale_price' => 'required|numeric|min:0|max:999999999',
            'expired_date' => 'required',
            'stock' => 'required',
        ]);

        // dd($request->all());

        // menginput data ke table products
        // dd($validated);
        Product::create($validated);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('barang.index')->with('success', 'Product created successfully.');
    }


    public function edit(Request $request, Barang $barang, $type = null)
    {
        // menyeleksi data product berdasarkan id yang dipilih
        $categories = Jenis::get();
        // $merks = Merk::orderBy('name')->get();
        $title = 'POS TOKO | Barang';
        $units = Satuan::orderBy('satuan')->get();
        $data = [
            'product' => $barang,
            'categories' => $categories,
            'units' => $units,
            'title' => $title,
            'type' => $type
        ];
        if ($request->ajax()) {
            return response()->json($data);
        }
        return view('product.update', $data);
    }

    public function update(Request $request, Barang $barang)
    {
        try {
            // menyeleksi data yang akan diinputkan
            if ($request->IdBarang == $barang->IdBarang) {
                $validated = $request->validate([
                    'IdBarang' => 'required',
                    'nmBarang' => 'required',
                    'satuan' => 'required',
                    'isi' => 'required',
                    'hargaPokok' => 'required|numeric|min:0|max:999999999',
                    'hargaJual' => 'required|numeric|min:0|max:999999999',
                    'hargaGrosir' => 'required|numeric|min:0|max:999999999',
                    'stok' => 'required',
                    'expDate' => 'required',
                    'jenis' => 'required',
                    // 'merk_id' => 'required',
                    // 'discount' => 'required|numeric|min:0|max:999999999',
                ]);
            } else {
                $validated = $request->validate([
                    'IdBarang' => 'required|unique:t_barang',
                    'nmBarang' => 'required',
                    'satuan' => 'required',
                    'isi' => 'required',
                    'hargaPokok' => 'required|numeric|min:0|max:999999999',
                    'hargaJual' => 'required|numeric|min:0|max:999999999',
                    'hargaGrosir' => 'required|numeric|min:0|max:999999999',
                    'stok' => 'required',
                    'expDate' => 'required',
                    'jenis' => 'required',
                ]);
            }


            // mengupdate data di table products
            Barang::where('IdBarang', $barang->IdBarang)->update($validated);
            // dd($validated);

            // jika data berhasil ditambahkan, akan kembali ke halaman utama
            if ($request->type == 'expired') {
                return redirect()->route('laporan.kadaluarsa')->with('success', 'Produk berhasil diupdate');
            } else if ($request->type == 'empty') {
                return redirect()->route('laporan.habis')->with('success', 'Produk berhasil diupdate');
            } else {
                return redirect()->route('barang.index')->with('success', 'Produk berhasil diupdate');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->type == 'expired') {
                return redirect()->route('laporan.kadaluarsa')->with('error', $e->validator->errors()->first());
            } else if ($request->type == 'empty') {
                return redirect()->route('laporan.habis')->with('error', $e->validator->errors()->first());
            } else {
                return redirect()->route('barang.index')->with('error', $e->validator->errors()->first());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // menghapus data product berdasarkan id yang dipilih
        Product::destroy($id);

        // jika data berhasil dihapus, akan kembali ke halaman utama
        return response(null, 200);
    }
}
