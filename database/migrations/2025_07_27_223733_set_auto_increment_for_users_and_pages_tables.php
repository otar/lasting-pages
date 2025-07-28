<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 238328');
            DB::statement('ALTER TABLE pages AUTO_INCREMENT = 238328');
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement('UPDATE sqlite_sequence SET seq = 238327 WHERE name = "users"');
            DB::statement('UPDATE sqlite_sequence SET seq = 238327 WHERE name = "pages"');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER SEQUENCE users_id_seq RESTART WITH 238328');
            DB::statement('ALTER SEQUENCE pages_id_seq RESTART WITH 238328');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
