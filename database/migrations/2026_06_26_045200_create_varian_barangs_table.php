<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('varian_barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('warna', 50)->nullable();
            $table->string('ukuran', 20)->nullable();

            $table->string('sku', 100)->unique()->nullable();

            $table->decimal('harga_jual', 12, 2);

            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('varian_barang');
    }
};