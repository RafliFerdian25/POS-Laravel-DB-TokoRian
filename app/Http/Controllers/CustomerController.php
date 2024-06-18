<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Event\Code\Throwable;

class CustomerController extends Controller
{
    public function index()
    {
        $title = 'POS TOKO | Daftar Gas';

        $data = [
            'title' => $title,
            'currentNav' => 'customer',
        ];

        return view('customer.index', $data);
    }

    public function getCustomers(Request $request)
    {
        $customers = Customer::when($request->filterName, function ($query) use ($request) {
            $query->where('nama', 'like', "%$request->filterName%");
        })
            ->orderBy('nama', 'desc')
            ->get(['id', 'nama', 'alamat', 'telpon', 'nik']);

        return ResponseFormatter::success([
            'customers' => $customers
        ], 'Data customer berhasil diambil');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required|numeric',
            'nik' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        try {
            DB::beginTransaction();
            $customer = Customer::create([
                'nama' => $request->name,
                'alamat' => $request->address,
                'telpon' => $request->phone,
                'nik' => $request->nik,
            ]);
            DB::commit();

            return ResponseFormatter::success([
                'customer' => $customer
            ], 'Data customer berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data pelanggan gagal ditambahkan', 500);
        }
    }

    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required|numeric',
            'nik' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        try {
            DB::beginTransaction();
            $customer->update([
                'nama' => $request->name,
                'alamat' => $request->address,
                'telpon' => $request->phone,
                'nik' => $request->nik,
            ]);
            DB::commit();

            return ResponseFormatter::success([
                'customer' => $customer
            ], 'Data customer berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data customer gagal diubah', 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();
            $customer->delete();
            DB::commit();

            return ResponseFormatter::success(null, 'Data pelanggan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data pelanggan gagal dihapus');
        }
    }
}