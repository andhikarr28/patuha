<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_id')
                  ->constrained('kategori')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->string('nama_barang',150);
            $table->string('artikel',50)->nullable();
            $table->string('kode_seri',50)->nullable();
            $table->string('brand',100)->nullable();

            $table->text('spesifikasi')->nullable();

            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};