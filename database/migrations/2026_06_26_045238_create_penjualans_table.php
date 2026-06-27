<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();

            $table->string('no_nota')->unique();

            $table->date('tanggal_penjualan');

            $table->enum('channel',[
                'offline',
                'shopee',
                'tokopedia',
                'tiktok'
            ])->default('offline');

            $table->decimal('total',12,2)->default(0);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};