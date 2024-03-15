<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

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
        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/merk', [
            'merks' => $merks
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // // Jenis
        $this->info('Data Jenis Mulai Di Upload');
        $categories = DB::table('p_jenis')->get();
        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/category', [
            'categories' => $categories
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // Satuan
        $this->info('Data Satuan Mulai Di Upload');
        $units = DB::table('p_satuan')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/unit', [
            'units' => $units
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // Barang
        $this->info('Data Barang Mulai Di Upload');
        $products = DB::table('t_barang')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/product', [
            'products' => $products
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // Barang Dicari
        $this->info('Data Barang Dicari Mulai Di Upload');
        $searchProducts = DB::table('t_barang_dicari')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/search-product', [
            'searchProducts' => $searchProducts
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }

        // // Belanja
        $this->info('Data Belanja Mulai Di Upload');
        $shopping = DB::table('t_belanja')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/shopping', [
            'shopping' => $shopping
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // // Kasir
        $this->info('Data Kasir Mulai Di Upload');
        $lastThreeMonth = Carbon::now()->subMonths(3);
        $sale = DB::table('t_kasir')
            ->where('tanggal', '>', $lastThreeMonth)
            ->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/sale', [
            'sale' => $sale
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // pembelian
        $this->info('Data Pembelian Mulai Di Upload');
        $purchase = DB::table('t_pembelian')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/purchase', [
            'purchase' => $purchase
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }

        // pembelian detail
        $this->info('Data Detail Pembelian Mulai Di Upload');
        $purchaseDetail = DB::table('t_pembelian_detail')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/purchase-detail', [
            'purchaseDetail' => $purchaseDetail
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }

        // // Piutang
        $this->info('Data Piutang Mulai Di Upload');
        $receivables = DB::table('t_piutang')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/receivable', [
            'receivables' => $receivables
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }


        // Supplier
        $this->info('Data Supplier Mulai Di Upload');
        $suppliers = DB::table('t_supplier')->get();

        $response = Http::post(env('HOSTING_DOMAIN') . '/api/upload-data/supplier', [
            'suppliers' => $suppliers
        ]);

        $data = $response->json();
        if ($response->successful()) {
            $this->info('Response:');
            $this->line(json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . json_encode($data['meta']['message'], JSON_PRETTY_PRINT));
            $this->error('Error: ' . json_encode($data['data']['error'], JSON_PRETTY_PRINT));
        }

        $this->info('Semua Data Berhasil Di Upload');

        return 0;
    }
}
