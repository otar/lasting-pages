<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public const START_SEQUENCE_FROM = 240000;

    public function up(): void
    {
        $queries = [];
        switch (DB::getDriverName()) {
            case 'sqlite':
                // For SQLite, we need to insert entries into sqlite_sequence if they don't exist
                $queries[] = 'INSERT OR IGNORE INTO sqlite_sequence (name, seq) VALUES ("users", '.self::START_SEQUENCE_FROM.')';
                $queries[] = 'INSERT OR IGNORE INTO sqlite_sequence (name, seq) VALUES ("pages", '.self::START_SEQUENCE_FROM.')';

                // Then update them to ensure they have the correct value
                $queries[] = 'UPDATE sqlite_sequence SET seq = '.self::START_SEQUENCE_FROM.' WHERE name = "users"';
                $queries[] = 'UPDATE sqlite_sequence SET seq = '.self::START_SEQUENCE_FROM.' WHERE name = "pages"';
                break;

            case 'mysql':
                $queries[] = 'ALTER TABLE users AUTO_INCREMENT = '.self::START_SEQUENCE_FROM;
                $queries[] = 'ALTER TABLE pages AUTO_INCREMENT = '.self::START_SEQUENCE_FROM;
                break;

            case 'pgsql':
                $queries[] = 'ALTER SEQUENCE users_id_seq RESTART WITH '.self::START_SEQUENCE_FROM;
                $queries[] = 'ALTER SEQUENCE pages_id_seq RESTART WITH '.self::START_SEQUENCE_FROM;
                break;
        }

        foreach ($queries as $query) {
            DB::statement($query);
        }
    }
};
