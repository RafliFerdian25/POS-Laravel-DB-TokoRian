<?php

namespace App\Http\Controllers;

use App\Helpers\FormatDate;
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
        $title = "POS TOKO | Supplier";
        $data = [
            "title" => $title,
            "currentNav" => "supplier"
        ];

        return view("supplier.index", $data);
    }

    public function data()
    {
        $suppliers = Supplier::get()->map(function ($supplier) {
            $supplier->jadwal = $supplier->jadwal ? FormatDate::day($supplier->jadwal) : null;
            return $supplier;
        })->toArray();


        return ResponseFormatter::success([
            "suppliers" => $suppliers
        ], "Data berhasil diambil");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "POS TOKO | Supplier";
        $data = [
            "title" => $title,
            "currentNav" => "supplier"
        ];
        return view("supplier.create", $data);
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
            "telp" => "nullable|numeric|unique:t_supplier,telp",
            "email" => "nullable|email|unique:t_supplier,email",
            "jadwal" => "nullable|numeric",
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                "error" => $validated->errors()->first()
            ], "Data gagal divalidasi", 422);
        }

        try {
            DB::beginTransaction();
            Supplier::create($request->all());
            DB::commit();

            return ResponseFormatter::success([
                "message" => "Data Supplier Berhasil Ditambahkan",
                "redirect" => route("supplier.index")
            ], "Data berhasil ditambahkan", 201);
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
    public function edit(Supplier $supplier)
    {
        return ResponseFormatter::success([
            "supplier" => $supplier
        ], "Data berhasil diambil");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $rules = [
            "name" => "required|max:100",
            "product" => "required|max:100",
            "address" => "required",
            "city" => "required|max:25",
            "phone" => "nullable|numeric|unique:t_supplier,telp," . $supplier->IdSupplier . ",IdSupplier",
            "email" => "nullable|email|unique:t_supplier,email," . $supplier->IdSupplier . ",IdSupplier",
            "schedule" => "nullable|numeric",
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error([
                "error" => $validated->errors()->first()
            ], "Data gagal divalidasi", 422);
        }

        $supplier->update([
            "Nama" => $request->name,
            "Produk" => $request->product,
            "alamat" => $request->address,
            "kota" => $request->city,
            "telp" => $request->phone,
            "email" => $request->email,
            "jadwal" => $request->schedule
        ]);

        return ResponseFormatter::success([
            "message" => "Data Supplier Berhasil Diubah",
        ], "Data berhasil diubah");
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

        return ResponseFormatter::success([
            "message" => "Data Supplier Berhasil Dihapus"
        ], "Data berhasil dihapus");
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
