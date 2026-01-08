<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('candidates')) {
            return;
        }

        if (Schema::hasColumn('candidates', 'election_id')) {
            if (DB::getDriverName() === 'sqlite') {
                Schema::table('candidates', function (Blueprint $table) {
                    $table->unsignedBigInteger('election_id')->nullable()->change();
                });
            } else {
                try {
                    DB::statement('ALTER TABLE `candidates` MODIFY `election_id` bigint unsigned NULL');
                } catch (\Throwable $e) {
                    // ignore if MODIFY not supported on this platform/version
                }
            }
        } else {
            Schema::table('candidates', function (Blueprint $table) {
                $table->unsignedBigInteger('election_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('candidates') || ! Schema::hasColumn('candidates', 'election_id')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `candidates` MODIFY `election_id` bigint unsigned NOT NULL');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
