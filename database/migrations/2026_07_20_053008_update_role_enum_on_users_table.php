<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role
            ENUM('kasir', 'admin', 'owner')
            NOT NULL
            DEFAULT 'kasir'
        ");
    }

    public function down(): void
    {
        /*
        | Sebelum rollback, ubah user kasir menjadi admin
        | agar ENUM lama tetap valid.
        */

        DB::table('users')
            ->where('role', 'kasir')
            ->update([
                'role' => 'admin'
            ]);

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role
            ENUM('admin', 'owner')
            NOT NULL
            DEFAULT 'admin'
        ");
    }
};