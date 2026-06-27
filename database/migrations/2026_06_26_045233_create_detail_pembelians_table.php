<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pembelian_id')
                ->constrained('pembelian')
                ->cascadeOnDelete();

            $table->foreignId('varian_id')
                ->constrained('varian_barang')
                ->cascadeOnDelete();

            $table->integer('qty');

            $table->decimal('harga_satuan',12,2);

            $table->decimal('diskon',12,2)->default(0);

            $table->decimal('subtotal',12,2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};