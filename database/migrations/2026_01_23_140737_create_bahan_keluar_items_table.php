<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// tambahin transaction agar jika error semua dibatalkan

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bahan_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_keluar_id')
                ->constrained('bahan_keluars')
                ->cascadeOnDelete();
            $table->foreignId('bahan_id')
                ->constrained('bahans');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_keluar_items');
    }
};
