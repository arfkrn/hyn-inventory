<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->index('tanggal');
        });

        Schema::table('bahan_keluars', function (Blueprint $table) {
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });

        Schema::table('bahan_keluars', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });
    }
};
