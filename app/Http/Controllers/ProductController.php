<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Models\Barcode;
use App\Models\Category;
use App\Models\Merk;
use App\Models\Unit;
use App\Models\Toko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Exception;
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
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Barang',
            'categories' => Category::get(),
        ];

        return view('product.index', $data);
    }
    public function data(Request $request)
    {
        $query = Product::when($request->filterName != null, function ($query) use ($request) {
            return $query->where('nmBarang', 'LIKE', '%' . $request->filterName . '%');
        })
            ->when($request->filterBarcode != null, function ($query) use ($request) {
                return $query->where('IdBarang', 'LIKE', '%' . $request->filterBarcode . '%');
            })
            ->when($request->filterCategory != null, function ($query) use ($request) {
                return $query->where('jenis',  $request->filterCategory);
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
     * Mencari data produk berdasarkan nama atau barcode
     */
    public function searchData(Request $request)
    {
        $query = Product::select('IdBarang', 'nmBarang', 'hargaJual', 'hargaPokok')
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
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Barang',
            'categories' => Category::get(),
            'merks' => Merk::orderBy('merk')->get(),
            'units' => Unit::orderBy('satuan')->get(),
        ];

        return view('product.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'IdBarang' => 'required|unique:t_barang',
            'nmBarang' => 'required',
            'satuan' => 'required',
            'isi' => 'required',
            'hargaPokok' => 'required|numeric|min:0|max:999999999',
            'hargaJual' => 'required|numeric|min:0|max:999999999',
            'hargaGrosir' => 'required|numeric|min:0|max:999999999',
            'stok' => 'required|numeric|min:0',
            'rak' => 'required|numeric|min:0',
            'jenis' => 'required',
            'merk_id' => 'required',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()->first()
                ],
                'Data gagal ditambahkan',
                422
            );
        }

        try {
            DB::beginTransaction();
            // menginput data ke table products
            Product::create([
                'IdBarang' => $request->IdBarang,
                'nmBarang' => strtoupper($request->nmBarang),
                'satuan' => $request->satuan,
                'isi' => $request->isi,
                'hargaPokok' => $request->hargaPokok,
                'hargaJual' => $request->hargaJual,
                'hargaGrosir' => $request->hargaGrosir,
                'stok' => $request->stok,
                'Rak' => $request->rak,
                'jenis' => $request->jenis,
                'merk_id' => $request->merk_id,
                'expDate' => $request->expDate,
            ]);

            DB::commit();
            return ResponseFormatter::success(
                [
                    'redirect' => route('barang.index')
                ],
                'Data berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Data gagal ditambahkan',
                422
            );
        }
    }


    public function edit(Request $request, Product $product)
    {
        // menyeleksi data product berdasarkan id yang dipilih
        $categories = Category::get();
        // $merks = Merk::orderBy('name')->get();
        $title = 'POS TOKO | Barang';
        $units = Unit::orderBy('satuan')->get();
        $data = [
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'title' => $title,
        ];
        if ($request->ajax()) {
            return response()->json($data);
        }
        return view('product.update', $data);
    }

    public function update(Request $request, Product $product)
    {
        try {
            // menyeleksi data yang akan diinputkan
            if ($request->IdBarang == $product->IdBarang) {
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

            DB::beginTransaction();
            // mengupdate data di table products
            Product::where('IdBarang', $product->IdBarang)->update([
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

            DB::commit();
            // jika data berhasil ditambahkan, akan kembali ke halaman utama
            if ($request->type == 'empty') {
                return redirect()->route('barang.habis')->with('success', 'Produk berhasil diupdate');
            } else {
                if ($request->ajax()) {
                    return ResponseFormatter::success(
                        null,
                        'Data berhasil diupdate'
                    );
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->type == 'empty') {
                return redirect()->route('barang.habis')->with('error', $e->validator->errors()->first());
            } else {
                if ($request->ajax()) {
                    return ResponseFormatter::error(
                        [
                            'error' => $e->validator->errors()->first()
                        ],
                        'Data gagal diupdate',
                        422
                    );
                } else {
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            // menghapus data product berdasarkan id yang dipilih
            $product->delete();
            DB::commit();
            // jika data berhasil dihapus, akan kembali ke halaman utama
            return ResponseFormatter::success(null, 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Terjadi kesalahan saat menghapus data',
                500
            );
        }
    }

    /**
     * Melihat produk yang telah/akan expired
     */
    public function expired()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Laporan',
            'categories' => Category::get(),
        ];

        return view('product.expired', $data);
    }

    /**
     * Mnedapatkan data produk yang telah/akan expired
     */
    public function expiredData(Request $request)
    {
        $query = Product::select('IdBarang', 'nmBarang', 'expDate', 'stok')
            ->when($request->filterStartDate == null && $request->filterEndDate == null, function ($query) {
                return $query->where('expDate', '<=', Carbon::now()->addDays(90));
            })
            ->when($request->filterStartDate != null && $request->filterEndDate != null, function ($query) use ($request) {
                return $query->whereBetween('expDate', [$request->filterStartDate, $request->filterEndDate]);
            })
            ->when($request->filterName != null, function ($query) use ($request) {
                return $query->where('nmBarang', 'LIKE', '%' . $request->filterName . '%');
            })
            ->when($request->filterCategory != null, function ($query) use ($request) {
                return $query->where('jenis',  $request->filterCategory);
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
    public function empty()
    {
        $data = [
            'setting' => Toko::first(),
            'title' => 'POS TOKO | Barang Habis',
            'categories' => Category::get(),
        ];
        return view('product.empty', $data);
    }

    /**
     * Mendapatkan data produk yang stoknya kosong
     */
    public function emptyData(Request $request)
    {
        $query = Product::select('IdBarang', 'nmBarang', 'expDate', 'stok')
            ->when($request->filled('filterStock'), function ($query) use ($request) {
                return $query->where('stok', '<=', $request->filterStock);
            }, function ($query) {
                return $query->where('stok', '<=', 0);
            })
            ->when($request->filled('filterName'), function ($query) use ($request) {
                return $query->where('nmBarang', 'LIKE', '%' . $request->filterName . '%');
            })
            ->when($request->filled('filterCategory'), function ($query) use ($request) {
                return $query->where('jenis', $request->filterCategory);
            })
            ->orderBy('nmBarang', 'asc')
            ->limit(200);

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

        $product = Product::where('IdBarang', $request->IdBarang)->first();

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

    public function updateFromPrintPrice(Request $request, Product $product)
    {
        try {
            $rules = [
                'IdBarang' => 'required',
                'nmBarang' => 'required',
                'hargaJual' => 'required|numeric|min:0|max:999999999',
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

            DB::beginTransaction();

            $updateProduct = $this->update($request, $product);
            if ($updateProduct instanceof \Illuminate\Http\JsonResponse) {
                // Mengambil data dari JsonResponse
                $responseData = $updateProduct->getData();

                $meta = $responseData->meta;
                if ($meta->code != 200) {
                    throw new Exception('Terjadi Kesalahan. ' . $meta->message . '. Error: ' . $responseData->data->error, $meta->code);
                }
            }

            Barcode::where('idBarang', $product->IdBarang)->update([
                'idBarang' => $request->IdBarang,
                'nmBarang' => strtoupper($request->nmBarang),
                'hargaJual' => $request->hargaJual
            ]);

            DB::commit();

            return ResponseFormatter::success(null, 'Data barang berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                ['error' => $e->getMessage()],
                'Gagal mengubah data barang',
                $e->getCode()
            );
        }
    }
}
