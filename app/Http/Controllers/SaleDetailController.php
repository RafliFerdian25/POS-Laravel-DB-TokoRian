<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use DataTables;

class SaleDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id_penjualan = session('id_penjualan');
        $penjualan = Sale::where('id', $id_penjualan)->first();
        $penjualan_last = Sale::get()->last();
        if ($penjualan_last != null && $penjualan_last->status == false) {
            $id_penjualan = $penjualan_last->id;
            session(['id_penjualan' => $penjualan_last->id]);
        } else if (empty($id_penjualan) || !$penjualan || $penjualan_last->status == true) {
            $penjualan = new Sale();
            // $penjualan->id_user = auth()->id();
            $penjualan->user_id = 1;
            $penjualan->total_item = 0;
            $penjualan->total_price = 0;
            $penjualan->pay = 0;
            $penjualan->change = 0;
            $penjualan->profit = 0;
            $penjualan->discount = 0;
            $penjualan->status = false;
            $penjualan->save();

            session(['id_penjualan' => $penjualan->id]);
            $id_penjualan = $penjualan->id;
        }

        $product = Product::orderBy('name')->get();
        $title = 'POS TOKO | Kasir';
        return view('sale.index', compact('product', 'id_penjualan', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produk = Product::where('id', $request->id_produk)->first();
        // return response()->json("Data berhasil disimpan", 200);
        if (!$produk) {
            return response()->json('Data gagal disimpan', 404);
        }
        $saleDetail = SaleDetail::where('sale_id', $request->id_penjualan)
            ->where('product_id', $produk->id)
            ->first();
        if ($saleDetail) {
            $jumlah = new Request();
            $jumlah->replace(['jumlah' => $saleDetail->qty + 1]);
            $this->update($jumlah, $saleDetail->id);
            return response()->json('Data berhasil diubah', 200);
        }

        $detail = new SaleDetail();
        $detail->sale_id = $request->id_penjualan;
        $detail->product_id = $produk->id;
        $detail->selling_price = $produk->selling_price;
        $detail->purchase_price = $produk->purchase_price;
        $detail->qty = 1;
        $detail->discount = $produk->discount;
        $detail->subtotal = $produk->selling_price - $produk->discount;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
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
        $detail = SaleDetail::with('product')->find($id);
        $detail->qty = $request->jumlah;
        $detail->subtotal = $detail->product->selling_price * $request->jumlah - ($detail->discount * $request->jumlah);
        $detail->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = SaleDetail::find($id);
        $detail->delete();
        return response(null, 200);
    }

    public function data($id)
    {
        $detail = SaleDetail::with('product')
            ->where('sale_id', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;
        $diskon_produk = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">' . $item->product['id'] . '</span>';
            $row['nama_produk'] = $item->product['name'];
            $row['harga_jual']  = 'Rp. ' . number_format($item->product['selling_price'], 0, ',', '.');
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="' . $item->id . '" value="' . $item->qty . '">';
            $row['diskon']      = $item->discount;
            $row['subtotal']    = 'Rp. ' . number_format($item->subtotal, 0, ',', '.');
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`' . route('transaksi.destroy', $item->id) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            // $total += $item->product['selling_price'] * $item->qty - $item->discount * $item->qty;
            $total += $item->product['selling_price'] * $item->qty;
            $diskon_produk += $item->discount * $item->qty;
            // $total_tanpa_diskon += $item->product['selling_price'] * $item->qty * $item->qty;
            $total_item += $item->qty;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total d-none">' . $total . '</div>
                <div class="diskon_produk d-none">' . $diskon_produk . '</div>
                <div class="total_item d-none">' . $total_item . '</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return DataTables::of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah'])
            ->make(true);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0)
    {
        $bayar   = $total - $diskon;
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'subtotalrp' => number_format($total, 0, ',', '.'),
            'dibayar' => $bayar,
            'totalbayar' => number_format($bayar, 0, ',', '.'),
            // 'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => number_format($kembali, 0, ',', '.'),
            // 'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
        ];

        return response()->json($data);
    }
}