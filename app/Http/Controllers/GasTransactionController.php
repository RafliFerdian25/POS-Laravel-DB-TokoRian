<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\GasCustomer;
use App\Models\GasTransaction;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GasTransactionController extends Controller
{
    public function getGasTransactions($gasId)
    {
        $gasTransactions = GasTransaction::with(['gasCustomer:id,gas_id,pelanggan_id,kuota', 'gasCustomer.customer:id,nama'])
            ->whereHas('gasCustomer', function ($query) use ($gasId) {
                $query->where('gas_id', $gasId);
            })
            ->get();

        return ResponseFormatter::success([
            'gasTransactions' => $gasTransactions
        ], 'Data transaksi gas berhasil diambil');
    }
    public function store(Request $request)
    {
        $rules = [
            'gasId' => 'required',
            'gasCustomerId' => 'required',
            'payGas' => 'nullable|numeric',
            'emptyGas' => 'nullable|numeric',
            'takeGas' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        $gasCustomer = GasCustomer::find($request->gasCustomerId);

        // mengecek apakah data pelanggan gas ada
        if (!$gasCustomer) {
            return ResponseFormatter::error([
                'error' => 'Pelanggan gas tidak ditemukan'
            ], 'Data pelanggan gas tidak ditemukan', 404);
        }

        // ambil data transaksi pada pelanggan tersebut
        $gasTransactionCustomer = GasTransaction::select(DB::raw('SUM(bayar_tabung) as total_bayar_tabung, SUM(ambil_tabung) as total_ambil_tabung, SUM(tabung_kosong) as total_tabung_kosong'))
            ->where('gas_pelanggan_id', $gasCustomer->id)->first();

        // mengecek jumlah bayar gas tidak boleh lebih dari sisa kuota
        if ($request->payGas + ($gasTransactionCustomer->total_bayar_tabung ?? 0) > $gasCustomer->kuota) {
            return ResponseFormatter::error([
                'error' => 'Jumlah bayar gas tidak boleh lebih dari sisa kuota'
            ], 'Jumlah bayar gas tidak boleh lebih dari sisa kuota', 400);
        }

        // mengecek jumlah ambil tabung tidak boleh lebih dari sisa kuota
        if ($request->takeGas + ($gasTransactionCustomer->total_ambil_tabung ?? 0) > $gasCustomer->kuota) {
            return ResponseFormatter::error([
                'error' => 'Jumlah ambil tabung tidak boleh lebih dari sisa kuota'
            ], 'Jumlah ambil tabung tidak boleh lebih dari sisa kuota', 400);
        }

        // mengecek jumlah tabung kosong tidak boleh lebih dari sisa kuota
        if ($request->emptyGas + ($gasTransactionCustomer->total_tabung_kosong ?? 0) > $gasCustomer->kuota) {
            return ResponseFormatter::error([
                'error' => 'Jumlah tabung kosong tidak boleh lebih dari sisa kuota'
            ], 'Jumlah tabung kosong tidak boleh lebih dari sisa kuota', 400);
        }

        try {
            GasTransaction::create([
                'gas_pelanggan_id' => $gasCustomer->id,
                'bayar_tabung' => $request->payGas ?? 0,
                'ambil_tabung' => $request->takeGas ?? 0,
                'tabung_kosong' => $request->emptyGas ?? 0,
            ]);

            DB::commit();
            return ResponseFormatter::success([
                'gasCustomer' => $gasCustomer
            ], 'Data pelanggan gas berhasil diambil');
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data pelanggan gas gagal diambil', 500);
        }
    }

    public function update(Request $request, GasTransaction $gasTransaction)
    {
        $rules = [
            'gasCustomerIdUpdate' => 'required',
            'payGasUpdate' => 'nullable|numeric',
            'emptyGasUpdate' => 'nullable|numeric',
            'takeGasUpdate' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Validasi gagal', 422);
        }

        // ambil data pelanggan gas
        if ($request->gasCustomerIdUpdate != $gasTransaction->gas_pelanggan_id) {
            $gasCustomer = GasCustomer::find($request->gasCustomerIdUpdate);
        } else {
            $gasCustomer = GasCustomer::find($gasTransaction->gas_pelanggan_id);
        }

        // ambil data transaksi pada pelanggan tersebut
        $gasTransactionCustomer = GasTransaction::select(DB::raw('SUM(bayar_tabung) as total_bayar_tabung, SUM(ambil_tabung) as total_ambil_tabung, SUM(tabung_kosong) as total_tabung_kosong'))
            ->where('gas_pelanggan_id', $gasCustomer->id)->first();

        // menghitung sisa kuota
        $remainingPayGas = $gasCustomer->kuota - ($gasTransactionCustomer->total_bayar_tabung ?? 0);
        $remainingTakeGas = $gasCustomer->kuota - ($gasTransactionCustomer->total_ambil_tabung ?? 0);
        $remainingEmptyGas = $gasCustomer->kuota - ($gasTransactionCustomer->total_tabung_kosong ?? 0);

        // mengecek jumlah bayar gas tidak boleh lebih dari sisa kuota
        if (($request->payGasUpdate > $remainingPayGas)) {
            return ResponseFormatter::error([
                'error' => 'Jumlah bayar gas tidak boleh lebih dari sisa kuota'
            ], 'Gagal mengubah data transaksi gas', 400);
        }

        // mengecek jumlah ambil tabung tidak boleh lebih dari sisa kuota
        if ($request->takeGasUpdate > $remainingTakeGas) {
            return ResponseFormatter::error([
                'error' => 'Jumlah ambil tabung tidak boleh lebih dari sisa kuota'
            ], 'Gagal mengubah data transaksi gas', 400);
        }

        // mengecek jumlah tabung kosong tidak boleh lebih dari sisa kuota
        if ($request->emptyGasUpdate > $remainingEmptyGas) {
            return ResponseFormatter::error([
                'error' => 'Jumlah tabung kosong tidak boleh lebih dari sisa kuota'
            ], 'Gagal mengubah data transaksi gas', 400);
        }

        try {
            $gasTransaction->update([
                'gas_pelanggan_id' => $gasCustomer->id,
                'bayar_tabung' => $request->payGasUpdate ?? $gasTransaction->bayar_tabung,
                'ambil_tabung' => $request->takeGasUpdate ?? $gasTransaction->ambil_tabung,
                'tabung_kosong' => $request->emptyGasUpdate ?? $gasTransaction->tabung_kosong,
            ]);

            return ResponseFormatter::success([
                'gasTransaction' => $gasTransaction
            ], 'Data transaksi gas berhasil diubah');
        } catch (Throwable $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data transaksi gas gagal diubah', 500);
        }
    }

    public function destroy(GasTransaction $gasTransaction)
    {
        try {
            $gasTransaction->delete();
            return ResponseFormatter::success([
                'gasTransaction' => $gasTransaction
            ], 'Data transaksi gas berhasil dihapus');
        } catch (Throwable $e) {
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Data transaksi gas gagal dihapus', 500);
        }
    }
}
