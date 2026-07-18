<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('perencanaan_pembelian', function (Blueprint $table) {
            $table->id();

            $table->string('no_perencanaan')->unique();

            $table->date('tanggal_perencanaan');

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('supplier')
                ->nullOnDelete();

            $table->enum('status', [
                'draft',
                'direncanakan',
                'dipesan',
                'sebagian_diterima',
                'selesai',
                'dibatalkan'
            ])->default('draft');

            $table->text('catatan')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perencanaan_pembelian');
    }
};