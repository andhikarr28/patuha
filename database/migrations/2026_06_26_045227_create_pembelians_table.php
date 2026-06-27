<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();

            $table->string('no_faktur')->unique();

            $table->date('tanggal_pembelian');

            $table->foreignId('supplier_id')
                ->constrained('supplier')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('total_brutto',12,2)->default(0);
            $table->decimal('total_diskon',12,2)->default(0);
            $table->decimal('total_netto',12,2)->default(0);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};