<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add columns first
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

        if (!Schema::hasColumn('partylists', 'election_id')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->unsignedBigInteger('election_id')->nullable();
            });
        }

        // Add indexes after columns exist
        if (!$this->indexExists('partylists', 'partylists_status_index')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->index('status');
            });
        }

        if (!$this->indexExists('partylists', 'partylists_election_id_index')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->index('election_id');
            });
        }
    }

    public function down()
    {
        // Remove indexes first
        Schema::table('partylists', function (Blueprint $table) {
            if ($this->indexExists('partylists', 'partylists_status_index')) {
                $table->dropIndex(['status']);
            }

            if ($this->indexExists('partylists', 'partylists_election_id_index')) {
                $table->dropIndex(['election_id']);
            }
        });

        // Remove columns
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

        if (Schema::hasColumn('partylists', 'election_id')) {
            Schema::table('partylists', function (Blueprint $table) {
                $table->dropColumn('election_id');
            });
        }
    }

    private function indexExists($table, $index)
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
