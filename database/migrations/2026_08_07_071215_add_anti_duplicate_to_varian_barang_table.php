<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('varian_barang', function (Blueprint $table) {
            $table->unique(['barang_id', 'warna', 'ukuran'], 'unik_varian_per_barang');
        });
    }

    public function down(): void
    {
        Schema::table('varian_barang', function (Blueprint $table) {
            $table->dropUnique('unik_varian_per_barang');
        });
    }
};