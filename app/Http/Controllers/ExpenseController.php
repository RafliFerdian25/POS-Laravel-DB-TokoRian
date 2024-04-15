<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Expense;
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
            'currentNav' => 'expense',
        ];

        return view('expense.index', $data);
    }

    public function data()
    {
        $expenses = Expense::get();
        return ResponseFormatter::success(
            [
                'expenses' => $expenses
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
            Expense::create([
                'nama' => $request->name,
                'jumlah' => $request->amount,
            ]);

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
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return ResponseFormatter::success(
            null,
            'Data pengeluaran berhasil dihapus'
        );
    }
}
