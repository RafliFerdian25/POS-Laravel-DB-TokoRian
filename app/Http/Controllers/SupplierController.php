<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Merk;
use App\Models\Supplier;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::all();
        $title = "POS TOKO | Supplier";
        return view("supplier.index", compact("title", "suppliers"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "POS TOKO | Supplier";
        return view("supplier.create", compact("title"));
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
            "Nama" => "required|unique:t_supplier,Nama|max:100",
            "Produk" => "required|max:100",
            "alamat" => "required",
            "kota" => "required|max:25",
            "telp" => "numeric|unique:t_supplier,telp",
            "email" => "email|unique:t_supplier,email"
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                "message" => $validated->errors()->first()
            ], "Data gagal divalidasi", 422);
        }

        try {
            DB::beginTransaction();
            Supplier::create([
                "Nama" => $request->Nama,
                "Produk" => $request->Produk,
                "alamat" => $request->alamat,
                "kota" => $request->kota,
                "telp" => $request->telp,
                "email" => $request->email
            ]);
            DB::commit();

            return ResponseFormatter::success([
                "message" => "Data Supplier Berhasil Ditambahkan",
                "redirect" => route("supplier.index")
            ], "Data berhasil ditambahkan");
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                "message" => $e->getMessage()
            ], "Data gagal ditambahkan", 500);
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
        $title = "POS TOKO | Supplier";
        $supplier = Supplier::findOrFail($id);
        return view("supplier.update", compact("supplier", "title"));
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
        $validated = $request->validate([
            "name" => "required",
            "address" => "required",
            "phone" => "required|numeric|unique:suppliers,phone," . $id,
        ]);

        Supplier::whereId($id)->update($validated);

        return redirect()->route("supplier.index")->with("success", "Data Supplier Berhasil Diubah");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Supplier::destroy($id);

        return redirect()->route("supplier.index")->with("success", "Data Supplier Berhasil Dihapus");
    }

    /**
     * Search data from storage.
     */
    public function searchData(Request $request)
    {
        $suppliers = Supplier::where('Nama', 'like', '%' . $request->q . '%')
            ->orWhere('Produk', 'like', '%' . $request->q . '%')
            ->get();

        return ResponseFormatter::success([
            'suppliers' => $suppliers
        ], 'Data berhasil diambil');
    }
}
