<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class UploadDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer all data from local database to hosting database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // // Merk
        $this->info('Data Merk Mulai Di Upload');
        $merks = DB::table('p_merk')->get();

        $client = new Client();
        $response = $client->post('http://localhost:8000/upload-data', [
            'json' => ['merks' => $merks->all()]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->info('Data Merk Berhasil Di Upload', $data['status']);

        // foreach ($merks as $data) {
        //     DB::connection('hosting')->table('p_merk')->updateOrInsert(
        //         ['id' => $data->id],
        //         ['merk' => $data->merk, 'keterangan' => $data->keterangan],
        //     );
        // }



        // // Jenis
        // $this->info('Data Jenis Mulai Di Upload');
        // $categories = DB::table('p_jenis')->get();

        // foreach ($categories as $data) {
        //     DB::connection('hosting')->table('p_jenis')->updateOrInsert(
        //         ['ID' => $data->ID],
        //         ['jenis' => $data->jenis, 'keterangan' => $data->keterangan],
        //     );
        // }
        // $this->info('Data Jenis Berhasil Di Upload');


        // // Satuan
        // $this->info('Data Satuan Mulai Di Upload');
        // $units = DB::table('p_satuan')->get();

        // foreach ($units as $data) {
        //     DB::connection('hosting')->table('p_satuan')->updateOrInsert(
        //         ['ID' => $data->ID],
        //         ['satuan' => $data->satuan, 'keterangan' => $data->keterangan],
        //     );
        // }
        // $this->info('Data Satuan Berhasil Di Upload');

        // // Barang
        // $this->info('Data Barang Mulai Di Upload');
        // $products = DB::table('t_barang')->get();

        // foreach ($products as $data) {
        //     DB::connection('hosting')->table('t_barang')->updateOrInsert(
        //         ['IdBarang' => $data->IdBarang],
        //         [
        //             'nmBarang' => $data->nmBarang,
        //             'jenis' => $data->jenis,
        //             'satuan' => $data->satuan,
        //             'isi' => $data->isi,
        //             'hargaPokok' => $data->hargaPokok,
        //             'hargaJual' => $data->hargaJual,
        //             'hargaGrosir' => $data->hargaGrosir,
        //             'expDate' => $data->expDate,
        //             'Rak' => $data->Rak,
        //             'stok' => $data->stok,
        //             'merk_id' => $data->merk_id,
        //         ],
        //     );
        // }
        // $this->info('Data Barang Berhasil Di Upload');

        // // Barang Dicari
        // $this->info('Data Barang Dicari Mulai Di Upload');
        // $searchProducts = DB::table('t_barang_dicari')->get();

        // foreach ($searchProducts as $data) {
        //     DB::connection('hosting')->table('t_barang_dicari')->updateOrInsert(
        //         ['id' => $data->id],
        //         [
        //             'product_id' => $data->product_id,
        //             'created_at' => $data->created_at,
        //             'updated_at' => $data->updated_at,
        //         ],
        //     );
        // }
        // $this->info('Data Barang Dicari Berhasil Di Upload');

        // // Belanja
        // $this->info('Data Belanja Mulai Di Upload');
        // $shopping = DB::table('t_belanja')->get();

        // foreach ($shopping as $data) {
        //     DB::connection('hosting')->table('t_belanja')->updateOrInsert(
        //         ['id' => $data->id],
        //         [
        //             'IdBarang' => $data->IdBarang,
        //             'nmBarang' => $data->nmBarang,
        //             'satuan' => $data->satuan,
        //             'jumlah' => $data->jumlah,
        //             'hargaPokok' => $data->hargaPokok,
        //             'TOTAL' => $data->TOTAL,
        //         ],
        //     );
        // }
        // $this->info('Data Belanja Berhasil Di Upload');

        // // Kasir
        // $this->info('Data Kasir Mulai Di Upload');
        // $sale = DB::table('t_kasir')->get();

        // foreach ($sale as $data) {
        //     DB::connection('hosting')->table('t_kasir')->updateOrInsert(
        //         ['ID' => $data->ID],
        //         [
        //             'noUrut' => $data->noUrut,
        //             'noTransaksi' => $data->noTransaksi,
        //             'tanggal' => $data->tanggal,
        //             'idBarang' => $data->idBarang,
        //             'nmBarang' => $data->nmBarang,
        //             'jumlah' => $data->jumlah,
        //             'satuan' => $data->satuan,
        //             'harga' => $data->harga,
        //             'total' => $data->total,
        //             'Laba' => $data->Laba,
        //             'Bayar' => $data->Bayar,
        //         ],
        //     );
        // }
        // $this->info('Data Kasir Berhasil Di Upload');

        // // pembelian
        // $this->info('Data Pembelian Mulai Di Upload');
        // $purchase = DB::table('t_pembelian')->get();

        // foreach ($purchase as $data) {
        //     DB::connection('hosting')->table('t_pembelian')->updateOrInsert(
        //         ['id' => $data->id],
        //         [
        //             'supplier_id' => $data->supplier_id,
        //             'total' => $data->total,
        //             'amount' => $data->amount,
        //             'created_at' => $data->created_at,
        //             'updated_at' => $data->updated_at,
        //         ],
        //     );
        // }
        // $this->info('Data Pembelian Berhasil Di Upload');

        // // pembelian detail
        // $this->info('Data Detail Pembelian Mulai Di Upload');
        // $purchaseDetail = DB::table('t_pembelian_detail')->get();

        // foreach ($purchaseDetail as $data) {
        //     DB::connection('hosting')->table('t_pembelian_detail')->updateOrInsert(
        //         ['id' => $data->id],
        //         [
        //             'product_id' => $data->product_id,
        //             'quantity' => $data->quantity,
        //             'exp_date' => $data->exp_date,
        //             'exp_date_old' => $data->exp_date_old,
        //             'cost_of_good_sold' => $data->cost_of_good_sold,
        //             'cost_of_good_sold_old' => $data->cost_of_good_sold_old,
        //             'purchase_id' => $data->purchase_id,
        //             'sub_amount' => $data->sub_amount,
        //         ],
        //     );
        // }
        // $this->info('Data Detail Pembelian Berhasil Di Upload');

        // // Piutang
        // $this->info('Data Piutang Mulai Di Upload');
        // $receivables = DB::table('t_piutang')->get();

        // foreach ($receivables as $data) {
        //     DB::connection('hosting')->table('t_piutang')->updateOrInsert(
        //         ['noTransaksi' => $data->noTransaksi],
        //         [
        //             'tanggal' => $data->tanggal,
        //             'nama_utang' => $data->nama_utang,
        //             'JUMLAH' => $data->JUMLAH,
        //             'sts_bayar' => $data->sts_bayar,
        //         ],
        //     );
        // }
        // $this->info('Data Piutang Berhasil Di Upload');

        // // Supplier
        // $this->info('Data Supplier Mulai Di Upload');
        // $suppliers = DB::table('t_supplier')->get();

        // foreach ($suppliers as $data) {
        //     DB::connection('hosting')->table('t_supplier')->updateOrInsert(
        //         ['IdSupplier' => $data->IdSupplier],
        //         [
        //             'Nama' => $data->Nama,
        //             'Produk' => $data->Produk,
        //             'alamat' => $data->alamat,
        //             'kota' => $data->kota,
        //             'telp' => $data->telp,
        //             'email' => $data->email,
        //         ],
        //     );
        // }
        // $this->info('Data Supplier Berhasil Di Upload');

        // $this->info('Data Berhasil Di Upload');

        return 0;
    }
}
