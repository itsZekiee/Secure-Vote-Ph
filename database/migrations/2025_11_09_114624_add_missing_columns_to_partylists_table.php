<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add columns if missing
        if (!Schema::hasColumn('partylists', 'platform')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->text('platform')->nullable()->after('description');
            });
        }

        if (!Schema::hasColumn('partylists', 'status')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('deleted_at');
            });
        }

        // Add indexes if missing (works on MySQL)
        $database = DB::getDatabaseName();
        $indexRows = DB::select(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$database, 'partylists']
        );
        $indexNames = array_map(fn($r) => $r->INDEX_NAME, $indexRows);

        if (!in_array('partylists_status_index', $indexNames, true)) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->index('status');
            });
        }

        if (!in_array('partylists_election_id_index', $indexNames, true)) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->index('election_id');
            });
        }
    }

    public function down()
    {
        // Remove indexes if they exist
        $database = DB::getDatabaseName();
        $indexRows = DB::select(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$database, 'partylists']
        );
        $indexNames = array_map(fn($r) => $r->INDEX_NAME, $indexRows);

        Schema::table('partylists', function (Blueprint $table) use ($indexNames) {
            if (in_array('partylists_status_index', $indexNames, true)) {
                $table->dropIndex(['status']);
            }

            if (in_array('partylists_election_id_index', $indexNames, true)) {
                $table->dropIndex(['election_id']);
            }
        });

        // Remove columns if they exist
        if (Schema::hasColumn('partylists', 'platform')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->dropColumn('platform');
            });
        }

        if (Schema::hasColumn('partylists', 'status')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
