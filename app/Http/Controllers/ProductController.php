<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Barang;
use App\Models\Barcode;
use App\Models\Category;
use App\Models\Jenis;
use App\Models\Merk;
use App\Models\Satuan;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Validator;

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
        // $products = Barang::Select("id", "name", "unit", "purchase_price", "selling_price", "wholesale_price", "stock", "expired_date")->get();
        $products = Barang::get();

        return view('product.product', compact('products', 'title'));
    }
    public function data()
    {
        $product = Barang::Select("id", "name", "unit", "purchase_price", "selling_price", "wholesale_price", "stock", "expired_date")->get();
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
     * Mencari data produk berdasarkan nama atau barcode
     */
    public function searchData(Request $request)
    {
        $query = Barang::select('IdBarang', 'nmBarang', 'hargaJual')
            ->when($request->has('q'), function ($query) use ($request) {
                return $query->where('nmBarang', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('IdBarang', 'LIKE', '%' . $request->q . '%');
            })
            ->orderBy('nmBarang', 'asc')
            ->limit(100);

        $products = $query->get();
        $countProduct = $query->count();

        return ResponseFormatter::success(
            [
                'products' => $products,
                'countProduct' => $countProduct
            ],
            'Data berhasil diambil'
        );
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
        Barang::create($validated);

        // jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('barang.index')->with('success', 'Barang created successfully.');
    }


    public function edit(Request $request, Barang $barang)
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
                    'jenis' => 'required',
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
                    'jenis' => 'required',
                ]);
            }

            // mengupdate data di table products
            Barang::where('IdBarang', $barang->IdBarang)->update([
                'IdBarang' => $validated['IdBarang'],
                'nmBarang' => strtoupper($validated['nmBarang']),
                'satuan' => $validated['satuan'],
                'isi' => $validated['isi'],
                'hargaPokok' => $validated['hargaPokok'],
                'hargaJual' => $validated['hargaJual'],
                'hargaGrosir' => $validated['hargaGrosir'],
                'stok' => $validated['stok'],
                'jenis' => $validated['jenis'],
                'expDate' => $request->expDate,
            ]);

            // jika data berhasil ditambahkan, akan kembali ke halaman utama
            if ($request->type == 'expired') {
                return redirect()->route('barang.kadaluarsa')->with('success', 'Produk berhasil diupdate');
            } else if ($request->type == 'empty') {
                return redirect()->route('barang.habis')->with('success', 'Produk berhasil diupdate');
            } else {
                return ResponseFormatter::success(
                    null,
                    'Data berhasil diupdate'
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->type == 'expired') {
                return redirect()->route('barang.kadaluarsa')->with('error', $e->validator->errors()->first());
            } else if ($request->type == 'empty') {
                return redirect()->route('barang.habis')->with('error', $e->validator->errors()->first());
            } else {
                return ResponseFormatter::error(
                    [
                        'error' => $e->validator->errors()->first()
                    ],
                    'Data gagal diupdate',
                    422
                );
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
        Barang::destroy($id);

        // jika data berhasil dihapus, akan kembali ke halaman utama
        return response(null, 200);
    }

    /**
     * Melihat produk yang telah/akan expired
     */
    public function expired()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan',
        ];
        return view('product.expired', $data);
    }

    /**
     * Mnedapatkan data produk yang telah/akan expired
     */
    public function expiredData(Request $request)
    {
        $query = Barang::select('IdBarang', 'nmBarang', 'expDate', 'stok')
            ->when($request->filterStartDate == null && $request->filterEndDate == null, function ($query) {
                return $query->where('expDate', '<=', Carbon::now()->addDays(90));
            })
            ->when($request->filterStartDate != null && $request->filterEndDate != null, function ($query) use ($request) {
                return $query->whereBetween('expDate', [$request->filterStartDate, $request->filterEndDate]);
            })
            ->when($request->filterName != null, function ($query) use ($request) {
                return $query->where('nmBarang', 'LIKE', '%' . $request->filterName . '%');
            })
            ->orderBy('expDate', 'asc')
            ->limit(100);

        $products = $query->get();
        $countProduct = $query->count();

        return ResponseFormatter::success(
            [
                'products' => $products,
                'countProduct' => $countProduct
            ],
            'Data berhasil diambil'
        );
    }

    /**
     * Melihat produk yang stoknya kosong
     */
    public function productEmpty()
    {
        $title = 'POS TOKO | Laporan';

        $setting = Toko::first();

        $query = Barang::where('stok', '<', 5)
            ->orderBy('stok', 'asc');
        $products = $query->get();
        $countBarang = $query->count();
        $data = [
            'setting' => $setting,
            'title' => $title,
            'products' => $products,
            'countBarang' => $countBarang,
        ];
        return view('report.empty', $data);
    }

    /**
     * Melihat produk yang akan dicetak harganya
     */
    public function printPrice()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan',
        ];
        return view('product.printPrice', $data);
    }

    /**
     * Mnedapatkan data produk yang akan dicetak harganya
     */
    public function printPriceData(Request $request)
    {
        $query = Barcode::with('product:idBarang,expDate')
            ->orderBy('nmBarang', 'asc')
            ->limit(100);

        $products = $query->get();
        $countProduct = $query->count();

        return ResponseFormatter::success(
            [
                'products' => $products,
                'countProduct' => $countProduct
            ],
            'Data berhasil diambil'
        );
    }

    /**
     * Menambahkan data produk yang akan dicetak harganya
     */
    public function storePrintPrice(Request $request)
    {
        $rules = [
            'IdBarang' => 'required',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()
                ],
                'Data gagal ditambahkan',
                422
            );
        }

        $product = Barang::where('IdBarang', $request->IdBarang)->first();

        if (!$product) {
            return ResponseFormatter::error(
                [
                    'error' => 'Produk tidak ditemukan'
                ],
                'Data gagal ditambahkan',
                422
            );
        }

        try {
            Barcode::create([
                'IdBarang' => $product->IdBarang,
                'nmBarang' => $product->nmBarang,
                'hargaJual' => $product->hargaJual,
            ]);

            return ResponseFormatter::success(
                null,
                'Data berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Data gagal ditambahkan',
                422
            );
        }
    }

    public function destroyPrintPrice($id)
    {
        try {
            if ($id != 'all') {
                $product = Barcode::destroy($id);
            } else if ($id == 'all') {
                Barcode::truncate();
            }

            return ResponseFormatter::success(
                null,
                'Data berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Terjadi kesalahan saat menghapus data',
                500
            );
        }
    }
}
