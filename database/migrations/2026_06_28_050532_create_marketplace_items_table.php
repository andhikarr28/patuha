<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('marketplace_id')
                ->constrained('marketplace')
                ->cascadeOnDelete();

            $table->string('external_product_id');

            $table->string('nama_produk');

            $table->string('status')
                ->nullable();

            $table->decimal(
                'berat',
                10,
                2
            )->nullable();

            $table->bigInteger('kategori_id')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'marketplace_items'
        );
    }
};