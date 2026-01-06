<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        $tables = [
            'users' => ['id', 'organization_id'],
            'organizations' => ['id', 'created_by'],
            'elections' => ['id', 'organization_id', 'created_by'],
            'partylists' => ['id', 'organization_id', 'election_id', 'created_by'],
            'positions' => ['id', 'election_id'],
            'candidates' => ['id', 'election_id', 'position_id', 'user_id', 'organization_id', 'partylist_id', 'created_by'],
            'votes' => ['id', 'election_id', 'candidate_id', 'voter_id', 'position_id'],
            'voters' => ['id', 'election_id'],
            'settings' => ['id'],
            'election_user' => ['election_id', 'user_id'],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            Schema::table($table, function (Blueprint $table_bp) use ($table, $columns) {
                foreach ($columns as $column) {
                    if ($column === 'id') {
                        // Change primary key to UUID
                        // Note: This is tricky in some DBs.
                        // For a clean implementation in a development environment,
                        // it's often better to recreate the columns.
                        $table_bp->uuid($column)->change();
                    } else {
                        // Change foreign key to UUID
                        $table_bp->uuid($column)->nullable()->change();
                    }
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        // This is complex to reverse perfectly without knowing original types (mostly bigIncrements)
        Schema::enableForeignKeyConstraints();
    }
};
