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
        Schema::create('marketplace_mappings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('marketplace_item_model_id')
                ->constrained('marketplace_item_models')
                ->cascadeOnDelete();

            $table->foreignId('varian_id')
                ->constrained('varian_barang')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_mappings');
    }
};
