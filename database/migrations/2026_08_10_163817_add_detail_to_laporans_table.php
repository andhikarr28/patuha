<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->json('detail_transaksi_penjualan')->nullable()->after('barang_kurang_laku');
            $table->json('detail_transaksi_pembelian')->nullable()->after('detail_transaksi_penjualan');
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropColumn(['detail_transaksi_penjualan', 'detail_transaksi_pembelian']);
        });
    }
};