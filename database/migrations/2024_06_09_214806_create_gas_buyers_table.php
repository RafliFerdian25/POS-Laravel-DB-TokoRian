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
        Schema::create('gas_buyers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->text('address');
            $table->string('phone', 14);
            $table->string('nik', 16)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_buyers');
    }
};
