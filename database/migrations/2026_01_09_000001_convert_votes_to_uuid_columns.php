<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys if they exist by inspecting information_schema
        try {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'votes' AND REFERENCED_TABLE_NAME IS NOT NULL");
            foreach ($fks as $fk) {
                $fkName = $fk->CONSTRAINT_NAME ?? $fk->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `votes` DROP FOREIGN KEY `{$fkName}`");
                } catch (\Throwable $e) {
                    // ignore missing or invalid fk
                }
            }
        } catch (\Throwable $e) {
            // ignore inspection errors
        }

        // If primary key is integer, drop PK so we can modify column
        try {
            DB::statement('ALTER TABLE `votes` DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore
        }

        // Modify columns to char(36) to accept UUIDs
        DB::statement('ALTER TABLE `votes` MODIFY `id` CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE `votes` MODIFY `election_id` CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE `votes` MODIFY `voter_id` CHAR(36) NOT NULL');
        DB::statement('ALTER TABLE `votes` MODIFY `candidate_id` CHAR(36) NULL');
        DB::statement('ALTER TABLE `votes` MODIFY `position_id` CHAR(36) NOT NULL');

        // Recreate primary key
        try {
            DB::statement('ALTER TABLE `votes` ADD PRIMARY KEY (`id`)');
        } catch (\Throwable $e) {
            // ignore
        }

        // Recreate foreign keys (use set null for candidate to allow abstain)
        Schema::table('votes', function (Blueprint $table) {
            try { $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade'); } catch (\Throwable $e) {}
            try { $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade'); } catch (\Throwable $e) {}
            try { $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('set null'); } catch (\Throwable $e) {}
            try { $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            try { $table->dropForeign(['election_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['voter_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['candidate_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['position_id']); } catch (\Throwable $e) {}
        });

        // Attempt to revert to bigint (may fail if UUIDs present)
        try { DB::statement('ALTER TABLE `votes` DROP PRIMARY KEY'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `votes` MODIFY `id` bigint unsigned NOT NULL AUTO_INCREMENT'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `votes` MODIFY `election_id` bigint unsigned NOT NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `votes` MODIFY `voter_id` bigint unsigned NOT NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `votes` MODIFY `candidate_id` bigint unsigned NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `votes` MODIFY `position_id` bigint unsigned NOT NULL'); } catch (\Throwable $e) {}

        try { DB::statement('ALTER TABLE `votes` ADD PRIMARY KEY (`id`)'); } catch (\Throwable $e) {}

        Schema::table('votes', function (Blueprint $table) {
            try { $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade'); } catch (\Throwable $e) {}
            try { $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade'); } catch (\Throwable $e) {}
            try { $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade'); } catch (\Throwable $e) {}
            try { $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade'); } catch (\Throwable $e) {}
        });
    }
};
