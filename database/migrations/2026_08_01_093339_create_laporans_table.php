<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan')->unique();

            $table->date('periode_awal');
            $table->date('periode_akhir');

            $table->unsignedInteger('jumlah_transaksi_penjualan')->default(0);
            $table->decimal('total_penjualan_toko', 15, 2)->default(0);
            $table->decimal('total_penjualan_marketplace', 15, 2)->default(0);

            $table->unsignedInteger('jumlah_transaksi_pembelian')->default(0);
            $table->decimal('total_pembelian', 15, 2)->default(0);

            $table->text('catatan_evaluasi')->nullable();
            $table->text('keputusan_owner')->nullable();

            $table->enum('status', ['draft', 'terkirim', 'ditinjau'])->default('draft');

            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('ditinjau_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporans');
    }
};