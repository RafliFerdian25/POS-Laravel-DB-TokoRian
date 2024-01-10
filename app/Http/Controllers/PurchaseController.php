<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Merk;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'POS TOKO | Belanja';
        $purchases = Purchase::Select("purchases.*", "users.username", "suppliers.name as supplier_name")
            ->leftJoin('users', 'users.id', '=', 'purchases.user_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->get();
        return view('purchase.purchase', compact('purchases', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'POS TOKO | Belanja';
        $suppliers = Supplier::get();
        return view('purchase.create', compact('suppliers', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required',
        ]);

        // User ID
        $validated['user_id'] = 1;

        $validated['total_item'] = 0;
        $validated['total_price'] = 0;

        $purchase = Purchase::create($validated);

        $id = $purchase->getKey();

        return redirect()->route('belanja.create.purchase-details', $id )->with('success', 'Belanja berhasil ditambahkan.');
    }

    public function createPurchaseDetails($id)
    {
        $title = 'POS TOKO | Belanja';
        $purchaseId = $id;
        $categories = Category::get();
        $merks = Merk::get();
        $product = Product::orderBy('name')->get();
        return view('purchase.create-purchase-details', compact('purchaseId', 'title', 'categories', 'merks', 'product'));
    }

    public function storePurchaseDetails($id)
    {
        $title = 'POS TOKO | Belanja';
        return redirect()->route('belanja');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Purchase::destroy($id);

        return response(null, 200);
    }
}
