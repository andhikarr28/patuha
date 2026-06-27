<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace', function (Blueprint $table) {
            $table->id();

            $table->string('nama_marketplace',50);

            $table->boolean('status')->default(true);

            $table->timestamp('last_sync')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace');
    }
};