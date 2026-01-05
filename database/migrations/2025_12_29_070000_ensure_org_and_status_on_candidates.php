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

        // Ensure organization_id exists
        if (! Schema::hasColumn('candidates', 'organization_id')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->index();
            });
        }

        // Ensure status exists; add with default 'pending' if missing
        if (! Schema::hasColumn('candidates', 'status')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->string('status')->default('pending')->index();
            });
        } else {
            // If status exists but is NOT NULL without default, make sure there's a default
            // Use raw SQL to alter column safely when supported
            try {
                DB::statement("ALTER TABLE `candidates` ALTER `status` SET DEFAULT 'pending'");
            } catch (\Throwable $e) {
                // Some MySQL versions use MODIFY
                try {
                    DB::statement("ALTER TABLE `candidates` MODIFY `status` varchar(255) DEFAULT 'pending'");
                } catch (\Throwable $ex) {
                    // ignore if not supported
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('candidates')) {
            return;
        }

        if (Schema::hasColumn('candidates', 'organization_id')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropIndex(['organization_id']);
                $table->dropColumn('organization_id');
            });
        }

        if (Schema::hasColumn('candidates', 'status')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });
        }
    }
};
