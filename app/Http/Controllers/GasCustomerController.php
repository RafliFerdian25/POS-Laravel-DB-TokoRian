<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Gas;
use App\Models\GasCustomer;
use App\Models\GasTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GasCustomerController extends Controller
{
    public function getGasCustomers($gasId, Request $request)
    {
        $gasCustomers = GasCustomer::select('id', 'gas_id', 'pelanggan_id', 'kuota')
            ->with(['customer:id,nama,nik', 'gasTransactions' => function ($query) {
                $query->select(
                    'gas_pelanggan_id',
                    DB::raw('sum(bayar_tabung) as total_bayar_tabung'),
                    DB::raw('sum(ambil_tabung) as total_ambil_tabung'),
                    DB::raw('sum(tabung_kosong) as total_tabung_kosong')
                )->groupBy('gas_pelanggan_id');
            }])
            ->where('gas_id', $gasId)
            ->when($request->filterName, function ($query) use ($request) {
                return $query->whereHas('customer', function ($query) use ($request) {
                    $query->where('nama', 'like', "%$request->filterName%");
                });
            })
            ->get();

        return ResponseFormatter::success([
            'gasCustomers' => $gasCustomers
        ], 'Data pelanggan berhasil diambil');
    }

    public function store(Request $request)
    {
        $rules = [
            'gasId' => 'required',
            'customerId' => 'required',
            'quota' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        // mengecek apakah data gas ada
        $gas = Gas::find($request->gasId);
        if (!$gas) {
            return ResponseFormatter::error([
                'error' => 'Gas tidak ditemukan'
            ], 'Data gas tidak ditemukan', 404);
        }

        // mengecek sisa kuota gas
        $totalQuota = GasCustomer::where('gas_id', $request->gasId)->sum('kuota');

        if ($totalQuota + $request->quota > $gas->stok) {
            return ResponseFormatter::error([
                'error' => 'Kuota gas tidak mencukupi'
            ], 'Kuota gas tidak mencukupi', 400);
        }

        try {
            DB::beginTransaction();
            // mengecek apakah data pelanggan gas sudah ada kemudian update kuota
            $gasCustomer = GasCustomer::where('gas_id', $request->gasId)
                ->where('pelanggan_id', $request->customerId)
                ->first();

            if ($gasCustomer) {
                $gasCustomer->kuota += $request->quota;
                $gasCustomer->save();
            } else {
                $gasCustomer = GasCustomer::create([
                    'gas_id' => $request->gasId,
                    'pelanggan_id' => $request->customerId,
                    'kuota' => $request->quota,
                ]);
            }

            DB::commit();
            return ResponseFormatter::success([
                'gasCustomer' => $gasCustomer
            ], 'Data customer berhasil ditambahkan');
        } catch (Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data customer gagal ditambahkan', 500);
        }
    }

    public function update(Request $request, GasCustomer $gasCustomer)
    {
        $rules = [
            'quotaUpdate' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        // mengambil data gas yang telah diambil
        $gasCustomer->load(['gasTransactions' => function ($query) {
            $query->select(
                'gas_pelanggan_id',
                DB::raw('sum(ambil_tabung) as total_ambil_tabung'),
            )->groupBy('gas_pelanggan_id')->first();
        }]);

        // mengecek apakah kuota kurang dari total ambil tabung
        if ($request->quotaUpdate < $gasCustomer->gasTransactions->first()->total_ambil_tabung) {
            return ResponseFormatter::error([
                'error' => 'Kuota tidak boleh kurang dari total ambil tabung'
            ], 'Gagal mengubah kuota gas pelanggan', 400);
        }

        // mengecek apakah kuota melebihi sisa kuota gas
        $gas = Gas::find($gasCustomer->gas_id);
        $totalQuota = GasCustomer::where('gas_id', $gasCustomer->gas_id)->sum('kuota');
        $remainingQuota = $gas->stok - $totalQuota;

        if ($request->quotaUpdate > $remainingQuota + $gasCustomer->kuota) {
            return ResponseFormatter::error([
                'error' => 'Kuota tidak boleh melebihi sisa kuota'
            ], 'Gagal mengubah kuota gas pelanggan', 400);
        }

        // mengubah kuota gas pelanggan
        try {
            DB::beginTransaction();
            $gasCustomer->kuota = $request->quotaUpdate;
            $gasCustomer->save();
            DB::commit();

            return ResponseFormatter::success(null, 'Data customer berhasil diubah');
        } catch (Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data customer gagal diubah', 500);
        }
    }

    public function destroy(GasCustomer $gasCustomer)
    {
        try {
            DB::beginTransaction();
            // menghapus data traksaksi berdasarkan data pelanggan gas
            GasTransaction::where('gas_pelanggan_id', $gasCustomer->id)->delete();

            // menghapus data pelanggan gas
            $gasCustomer->delete();
            DB::commit();

            return ResponseFormatter::success([
                'gasCustomer' => $gasCustomer
            ], 'Data customer berhasil dihapus');
        } catch (Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $th->getMessage()
            ], 'Data customer gagal dihapus', 500);
        }
    }
}