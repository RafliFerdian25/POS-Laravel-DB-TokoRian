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
        Schema::create('t_gas_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gas_id')->unsigned();
            $table->foreign('gas_id')->references('id')->on('t_gas');
            $table->bigInteger('pelanggan_id')->unsigned();
            $table->foreign('pelanggan_id')->references('id')->on('t_pelanggan');
            $table->integer('kuota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_gas_pelanggan');
    }
};