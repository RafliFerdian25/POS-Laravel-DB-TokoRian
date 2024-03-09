<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\ShoppingController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // // Belanja
        $this->info('Data Belanja Mulai Di Download');

        $shoppingController = new ShoppingController();
        $response = $shoppingController->downloadData();

        if ($response->original['meta']['status'] == 'success') {
            $this->info('Response:');
            $this->line($response->original['meta']['message']);
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . $response->original['meta']['message']);
            $this->error('Error: ' . $response->original['data']['error']);
        }

        $this->info('Data Barang Dicari Mulai Di Download');

        $productSearchController = new ProductSearchController();
        $response = $productSearchController->downloadData();

        if ($response->original['meta']['status'] == 'success') {
            $this->info('Response:');
            $this->line($response->original['meta']['message']);
        } else {
            $this->error('Status: ' . $response->status());
            $this->error('Pesan: ' . $response->original['meta']['message']);
            $this->error('Error: ' . $response->original['data']['error']);
        }

        $this->info('Semua Data Berhasil Di Download');
        return 0;
    }
}
