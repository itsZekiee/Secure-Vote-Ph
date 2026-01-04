<?php
// php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('elections')) {
            return;
        }

        Schema::table('elections', function (Blueprint $table) {
            if (!Schema::hasColumn('elections', 'access_code')) {
                $table->string('access_code', 20)->nullable()->after('title');
            }

            if (!Schema::hasColumn('elections', 'access_link')) {
                // place after access_code if present, otherwise after title
                $after = Schema::hasColumn('elections', 'access_code') ? 'access_code' : 'title';
                $table->string('access_link', 255)->nullable()->after($after);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('elections')) {
            return;
        }

        Schema::table('elections', function (Blueprint $table) {
            if (Schema::hasColumn('elections', 'access_link')) {
                $table->dropColumn('access_link');
            }
            if (Schema::hasColumn('elections', 'access_code')) {
                $table->dropColumn('access_code');
            }
        });
    }
};
