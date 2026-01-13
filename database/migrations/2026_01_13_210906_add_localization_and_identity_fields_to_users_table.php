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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('Asia/Manila')->after('department');
            }
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language')->default('en')->after('timezone');
            }
            if (!Schema::hasColumn('users', 'date_format')) {
                $table->string('date_format')->default('M d, Y')->after('language');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('date_format');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'department',
                'timezone',
                'language',
                'date_format',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};
