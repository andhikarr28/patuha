<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {

            $table->foreignId('perencanaan_pembelian_id')
                ->nullable()
                ->after('id')
                ->constrained('perencanaan_pembelian')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {

            $table->dropForeign([
                'perencanaan_pembelian_id'
            ]);

            $table->dropColumn(
                'perencanaan_pembelian_id'
            );

        });
    }
};