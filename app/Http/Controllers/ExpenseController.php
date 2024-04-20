<?php

namespace App\Http\Controllers;

use App\Helpers\FormatDate;
use App\Helpers\ResponseFormatter;
use App\Models\Expense;
use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Pengeluaran Toko';

        $data = [
            'title' => $title,
            'typeReport' => 'Bulanan',
            'currentNav' => 'expense',
        ];

        return view('expense.index', $data);
    }

    public function data(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $expenses = Expense::when($typeReport == 'Bulanan', function ($query) use ($date) {
            return $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
        })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return ResponseFormatter::success(
            [
                'typeReport' => $typeReport,
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'expenses' => $expenses
            ],
            'Pengeluaran berhasil diambil'
        );
    }

    /**
     * Get sum of expenses
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sumData(Request $request)
    {
        $typeReport = null;
        $date = null;
        $startDate = null;
        $endDate = null;

        if ($request->daterange == null && $request->month == null) {
            $date = Carbon::now();
            $typeReport = "Bulanan";
        } elseif ($request->daterange != null) {
            $daterange = explode(' - ', $request->daterange);
            $date = Carbon::parse($daterange[1]);
            $startDate = Carbon::parse($daterange[0]);
            $endDate = Carbon::parse($daterange[1]);
            $typeReport = "Harian";
        } elseif ($request->month != null) {
            $date = Carbon::parse($request->month);
            $typeReport = "Bulanan";
        }

        $expense = Expense::when($typeReport == 'Bulanan', function ($query) use ($date) {
            return $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
        })
            ->when($typeReport == 'Harian', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->sum('jumlah');

        return ResponseFormatter::success(
            [
                'typeReport' => $typeReport,
                'dateString' => $typeReport == 'Bulanan' ? FormatDate::month($date->month) : $startDate->copy()->format('d M Y') . ' - ' . $endDate->copy()->format('d M Y'),
                'date' => $typeReport == 'Bulanan' ? $date->format('Y-m') : $daterange,
                'expense' => $expense
            ],
            'Pengeluaran berhasil diambil'
        );
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Pengeluaran';
        $data = [
            'title' => $title,
            'currentNav' => 'expense',
        ];

        return view('expense.create', $data);
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
            'name' => 'required',
            'amount' => 'required|numeric|min:0|max:999999999',
            'place' => 'required|in:atas,bawah'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()->first()
                ],
                'Data pengeluaran gagal ditambahkan',
                422
            );
        }

        try {
            DB::beginTransaction();
            // menginput data ke table products
            Expense::create([
                'nama' => $request->name,
                'jumlah' => $request->amount,
                'tempat' => $request->place
            ]);

            if ($request->place == 'atas') {
                Finance::where('id', 1)->decrement('cash_atas', $request->amount);
            } else {
                Finance::where('id', 1)->decrement('cash_bawah', $request->amount);
            }

            DB::commit();
            return ResponseFormatter::success(
                [
                    'redirect' => route('expense.index')
                ],
                'Data pengeluaran berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Data pengeluaran gagal ditambahkan',
                422
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        // menyeleksi data kategori berdasarkan id yang dipilih
        return ResponseFormatter::success([
            'expense' => $expense
        ], 'Kategori berhasil diambil');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {

        $rules = [
            'name' => 'required',
            'amount' => 'required|numeric|min:0|max:999999999',
            'place' => 'required|in:atas,bawah'
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return ResponseFormatter::error(
                [
                    'error' => $validated->errors()->first()
                ],
                'Data pengeluaran gagal diubah',
                422
            );
        }

        try {
            DB::beginTransaction();

            // mengubah data pengeluaran
            $expense->update([
                'nama' => $request->name,
                'jumlah' => $request->amount,
                'tempat' => $request->place,
            ]);

            if ($request->place == 'atas') {
                Finance::where('id', 1)->decrement('cash_atas', $request->amount);
            } else {
                Finance::where('id', 1)->decrement('cash_bawah', $request->amount);
            }

            DB::commit();
            return ResponseFormatter::success(
                null,
                'Data pengeluaran berhasil diubah'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Data pengeluaran gagal diubah',
                422
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();
            if ($expense->tempat == 'atas') {
                Finance::where('id', 1)->increment('cash_atas', $expense->jumlah);
            } else {
                Finance::where('id', 1)->increment('cash_bawah', $expense->jumlah);
            }

            $expense->delete();
            DB::commit();
            return ResponseFormatter::success(
                null,
                'Data pengeluaran berhasil dihapus'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                [
                    'error' => $e->getMessage()
                ],
                'Data pengeluaran gagal dihapus',
                422
            );
        }
    }
}
