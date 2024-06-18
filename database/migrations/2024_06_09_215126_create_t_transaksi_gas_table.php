<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_transaksi_gas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gas_pelanggan_id')->unsigned();
            $table->foreign('gas_pelanggan_id')->references('id')->on('t_gas_pelanggan');
            $table->integer('bayar_tabung');
            $table->integer('tabung_kosong');
            $table->integer('ambil_tabung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transaksi_gas');
    }
};
