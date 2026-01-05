<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            if (!Schema::hasColumn('voters', 'student_id')) {
                $table->string('student_id')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            if (Schema::hasColumn('voters', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }
};
