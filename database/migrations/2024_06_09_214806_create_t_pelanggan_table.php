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
        Schema::create('t_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->text('alamat');
            $table->string('telpon', 14);
            $table->string('nik', 16)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_pelanggan');
    }
};