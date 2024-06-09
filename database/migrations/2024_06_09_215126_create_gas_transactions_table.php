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
        Schema::create('gas_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gas_id')->constrained('gases');
            $table->foreignId('gas_buyer_id')->constrained('gas_buyers');
            $table->integer('pay');
            $table->integer('empty_gas');
            $table->integer('take');
            $table->integer('quota');
            $table->date('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_transactions');
    }
};
