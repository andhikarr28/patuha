<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_log', function (Blueprint $table) {
            $table->id();

            $table->foreignId('varian_id')
                ->constrained('varian_barang')
                ->cascadeOnDelete();

            $table->enum('tipe_transaksi',[
                'pembelian',
                'penjualan',
                'penyesuaian'
            ]);

            $table->integer('qty');

            $table->integer('stok_sebelum');

            $table->integer('stok_sesudah');

            $table->string('referensi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_log');
    }
};