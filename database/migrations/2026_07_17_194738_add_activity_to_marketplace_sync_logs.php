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
        Schema::table(
            'marketplace_sync_logs',
            function (Blueprint $table) {

                $table->string('aktivitas')
                    ->nullable()
                    ->after('marketplace_id');

                $table->string('arah_sync')
                    ->nullable()
                    ->after('aktivitas');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_sync_logs', function (Blueprint $table) {
            //
        });
    }
};
