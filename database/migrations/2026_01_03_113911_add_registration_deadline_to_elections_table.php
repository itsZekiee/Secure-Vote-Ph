<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            if (!Schema::hasColumn('elections', 'registration_deadline')) {
                $table->dateTime('registration_deadline')->nullable()->after('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            if (Schema::hasColumn('elections', 'registration_deadline')) {
                $table->dropColumn('registration_deadline');
            }
        });
    }
};
