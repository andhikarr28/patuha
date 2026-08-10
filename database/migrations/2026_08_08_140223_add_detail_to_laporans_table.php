<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->json('barang_terlaris')->nullable()->after('total_pembelian');
            $table->json('barang_kurang_laku')->nullable()->after('barang_terlaris');
            $table->json('ringkasan_perencanaan')->nullable()->after('barang_kurang_laku');
            $table->unsignedInteger('jumlah_varian_stok_menipis')->default(0)->after('ringkasan_perencanaan');
            $table->decimal('estimasi_laba_kotor', 15, 2)->default(0)->after('jumlah_varian_stok_menipis');
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropColumn([
                'barang_terlaris',
                'barang_kurang_laku',
                'ringkasan_perencanaan',
                'jumlah_varian_stok_menipis',
                'estimasi_laba_kotor',
            ]);
        });
    }
};