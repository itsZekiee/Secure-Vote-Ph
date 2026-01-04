<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('candidates')) {
            return;
        }

        if (! Schema::hasColumn('candidates', 'organization_id')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('candidates') || ! Schema::hasColumn('candidates', 'organization_id')) {
            return;
        }

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
