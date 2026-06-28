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
        Schema::create('marketplace_item_models', function (Blueprint $table) {

            $table->id();

            $table->foreignId('marketplace_item_id')
                ->constrained('marketplace_items')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('model_id');

            $table->string('model_sku')->nullable();

            $table->integer('stok')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_item_models');
    }
};
