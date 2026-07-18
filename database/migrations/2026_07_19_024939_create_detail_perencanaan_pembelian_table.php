<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_perencanaan_pembelian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('perencanaan_pembelian_id')
                ->constrained('perencanaan_pembelian')
                ->cascadeOnDelete();

            $table->foreignId('varian_id')
                ->constrained('varian_barang')
                ->restrictOnDelete();

            $table->unsignedInteger('qty_rencana');

            $table->decimal('estimasi_harga', 15, 2)
                ->nullable();

            // Berapa unit dari rencana ini yang sudah benar-benar diterima.
            $table->unsignedInteger('qty_diterima')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'detail_perencanaan_pembelian'
        );
    }
};