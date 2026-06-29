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
        Schema::table('varian_barang', function ($table) {

            $table->decimal(
                'harga_beli',
                12,
                2
            )->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('varian_barang', function ($table) {

            $table->dropColumn(
                'harga_beli'
            );

        });
    }
};
