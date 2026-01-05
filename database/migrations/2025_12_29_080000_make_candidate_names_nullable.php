<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('candidates')) {
            return;
        }

        // Use raw ALTER statements to avoid requiring doctrine/dbal for `change()`
        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `first_name` varchar(255) NULL");
        } catch (\Throwable $e) {
            // ignore if modification not supported or column missing
        }

        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `last_name` varchar(255) NULL");
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `display_name` varchar(255) NULL");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('candidates')) {
            return;
        }

        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `first_name` varchar(255) NOT NULL");
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `last_name` varchar(255) NOT NULL");
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::statement("ALTER TABLE `candidates` MODIFY `display_name` varchar(255) NOT NULL");
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
