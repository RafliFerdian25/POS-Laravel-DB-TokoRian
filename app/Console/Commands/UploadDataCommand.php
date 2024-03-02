<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        // Jenis
        $category = DB::table('p_jenis')->get();

        foreach ($category as $data) {
            DB::connection('hosting')->table('p_jenis')->updateOrInsert(
                ['ID' => $data->ID],
                ['jenis' => $data->jenis, 'keterangan' => $data->keterangan],
            );
        }
        
        // Merk
        // Satuan
        // Barang
        // Barang Dicari
        // Belanja
        // Kasir
        // Piutang
        // Supplier

        $this->info('Data Berhasil Di Upload');
        return 0;
    }
}
