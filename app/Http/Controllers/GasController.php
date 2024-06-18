<?php

namespace App\Http\Controllers;

use App\Helpers\FilterRequest;
use App\Helpers\FormatDate;
use App\Helpers\ResponseFormatter;
use App\Models\Gas;
use App\Models\GasCustomer;
use App\Models\GasTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GasController extends Controller
{
    public function index()
    {
        $title = 'POS TOKO | Daftar Gas';
        $gas = Gas::latest()->first();

        if ($gas != null) {
            $amountGasPurchase = GasTransaction::whereBetween('created_at', [$gas->created_at, date('DD-MM-YYYY')])->sum('ambil_tabung');
            $remainingGas = $gas->stok - $amountGasPurchase;
        } else {
            $remainingGas = 0;
        }

        $data = [
            'title' => $title,
            'typeReport' => 'Bulanan',
            'currentNav' => 'gas',
            'remainingGas' => $remainingGas,
        ];

        return view('gas.index', $data);
    }

    public function getGases(Request $request)
    {
        $filterDate = FilterRequest::filterDate($request);
        $gasses = Gas::when($filterDate['typeReport'] == 'Bulanan', function ($query) use ($filterDate) {
            return $query->whereMonth('tanggal', $filterDate['date']->month)
                ->whereYear('tanggal', $filterDate['date']->year);
        })
            ->when($filterDate['typeReport'] == 'Harian', function ($query) use ($filterDate) {
                return $query->whereBetween('tanggal', [$filterDate['startDate'], $filterDate['endDate']]);
            })
            ->get();

        return ResponseFormatter::success([
            'gases' => $gasses,
            'dateString' => $filterDate['typeReport'] == 'Bulanan' ? FormatDate::month($filterDate['date']->month) : $filterDate['startDate']->copy()->format('d M Y') . ' - ' . $filterDate['endDate']->copy()->format('d M Y'),
            'date' => $filterDate['typeReport'] == 'Bulanan' ? $filterDate['date']->format('Y-m') : $filterDate['daterange'],
            'typeReport' => $filterDate['typeReport'],
        ], 'Data gas berhasil diambil');
    }

    public function show(Gas $gas)
    {
        $title = 'POS TOKO | Detail Transaksi Gas';

        $data = [
            'title' => $title,
            'currentNav' => 'gas',
            'gas' => $gas,
        ];

        return view('gas.show', $data);
    }

    public function getRemainingGas(Gas $gas)
    {
        $totalQuota = GasCustomer::where('gas_id', $gas->id)->sum('kuota');
        $gasCustomers = // Ambil data gas customer dengan total ambil_tabung
            $gasCustomers = GasCustomer::with(['gasTransactions' => function ($query) {
                $query->select(
                    'gas_pelanggan_id',
                    DB::raw('SUM(ambil_tabung) as total_ambil_tabung')
                )->groupBy('gas_pelanggan_id');
            }])
            ->where('gas_id', $gas->id)
            ->get(); // Gunakan get() untuk mendapatkan koleksi

        // Menghitung total ambil_tabung untuk setiap pelanggan
        $totalTakeGas = $gasCustomers->reduce(function ($carry, $customer) {
            return $carry + ($customer->gasTransactions->first()->total_ambil_tabung ?? 0);
        }, 0);

        $remainingQuota = $gas->stok - $totalQuota;
        $remainingGas = $gas->stok - $totalTakeGas;

        return ResponseFormatter::success([
            'remainingQuota' => $remainingQuota,
            'remainingGas' => $remainingGas,
        ], 'Data sisa gas berhasil diambil');
    }

    public function store(Request $request)
    {
        $rules = [
            'date' => 'required|date',
            'stock' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Gagal menyimpan data gas', 400);
        }

        $gas = Gas::create([
            'tanggal' => $request->date,
            'stok' => $request->stock,
        ]);

        return ResponseFormatter::success($gas, 'Data gas berhasil ditambahkan', 201);
    }

    public function update(Gas $gas, Request $request)
    {
        $rules = [
            'date' => 'required|date',
            'stock' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()->first()
            ], 'Gagal menyimpan data gas', 400);
        }

        try {
            DB::beginTransaction();
            $gas->update([
                'tanggal' => $request->date,
                'stok' => $request->stock,
            ]);
            DB::commit();
            return ResponseFormatter::success(null, 'Data gas berhasil diupdate', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Gagal menyimpan data gas', 500);
        }
    }

    public function destroy(Gas $gas)
    {
        try {
            DB::beginTransaction();
            $gas->delete();
            DB::commit();

            return ResponseFormatter::success(null, 'Data gas berhasil dihapus', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ], 'Gagal menghapus data gas', 500);
        }
    }
}