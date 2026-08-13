<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('varian_barang', function (Blueprint $table) {
            $table->decimal('margin_persen', 5, 2)->nullable()->after('harga_jual');
        });
    }

    public function down()
    {
        Schema::table('varian_barang', function (Blueprint $table) {
            $table->dropColumn('margin_persen');
        });
    }
};