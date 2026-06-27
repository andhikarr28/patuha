<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('varian_id')
                ->constrained('varian_barang')
                ->cascadeOnDelete();

            $table->foreignId('marketplace_id')
                ->constrained('marketplace')
                ->cascadeOnDelete();

            $table->string('external_product_id')->nullable();

            $table->string('external_sku')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_product');
    }
};