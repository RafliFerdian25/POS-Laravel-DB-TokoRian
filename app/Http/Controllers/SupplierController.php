<?php

namespace App\Http\Controllers;

use App\Models\Merk;
use App\Models\Supplier;
use Illuminate\Http\Request;

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
        $validated = $request -> validate([
            "name" => "required",
            "address" => "required",
            "phone" => "required|numeric|unique:suppliers",
        ]);

        Supplier::create($validated);

        return redirect() -> route("supplier.index") -> with("success", "Data Supplier Berhasil Ditambahkan");
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
        $validated = $request -> validate([
            "name" => "required",
            "address" => "required",
            "phone" => "required|numeric|unique:suppliers,phone," . $id,
        ]);

        Supplier::whereId($id)->update($validated);

        return redirect() -> route("supplier.index") -> with("success", "Data Supplier Berhasil Diubah");
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

        return redirect() -> route("supplier.index") -> with("success", "Data Supplier Berhasil Dihapus");
    }
}
