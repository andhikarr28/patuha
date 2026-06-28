<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketplace_sync_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('marketplace_id')
                ->constrained('marketplace')
                ->cascadeOnDelete();

            $table->integer('jumlah_produk');

            $table->integer('jumlah_varian');

            $table->timestamp('sync_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_sync_logs');
    }
};
