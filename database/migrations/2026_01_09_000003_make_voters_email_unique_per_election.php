<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing unique index on email and create composite unique (election_id, email)
        try {
            // Check if the unique index exists
            $exists = DB::select("SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'email'");
            if (!empty($exists)) {
                foreach ($exists as $row) {
                    $index = $row->INDEX_NAME ?? $row->INDEX_NAME;
                    // Drop only if it's the unique index on email
                    try {
                        DB::statement("ALTER TABLE `voters` DROP INDEX `{$index}`");
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Create composite unique index
        Schema::table('voters', function (Blueprint $table) {
            $table->unique(['election_id', 'email'], 'voters_election_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->dropUnique('voters_election_email_unique');
            $table->unique('email', 'voters_email_unique');
        });
    }
};
