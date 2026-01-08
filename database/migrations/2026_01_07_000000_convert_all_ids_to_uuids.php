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
        // 1. Get all tables and their columns to be converted
        $tables = [
            'users' => ['id', 'organization_id'],
            'organizations' => ['id', 'created_by'],
            'elections' => ['id', 'organization_id', 'created_by'],
            'partylists' => ['id', 'organization_id', 'election_id', 'created_by'],
            'positions' => ['id', 'election_id'],
            'candidates' => ['id', 'election_id', 'position_id', 'user_id', 'organization_id', 'partylist_id', 'created_by'],
            'failed_logins' => ['id', 'user_id', 'election_id'],
            'votes' => ['id', 'election_id', 'candidate_id', 'voter_id', 'position_id'],
            'voters' => ['id', 'election_id', 'user_id'],
            'settings' => ['id'],
            'election_user' => ['election_id', 'user_id'],
            'audit_logs' => ['id', 'user_id', 'model_id'],
        ];

        // 2. Drop all foreign keys first to avoid "Cannot change column" errors
        foreach (array_keys($tables) as $table) {
            if (!Schema::hasTable($table)) continue;

            $foreignKeys = $this->getTableForeignKeys($table);
            if (!empty($foreignKeys)) {
                Schema::table($table, function (Blueprint $table_bp) use ($foreignKeys) {
                    foreach ($foreignKeys as $fk) {
                        try {
                            $table_bp->dropForeign($fk);
                        } catch (\Exception $e) {
                            // Ignore if already dropped
                        }
                    }
                });
            }
        }

        // 3. Change columns to UUID (string)
        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            Schema::table($table, function (Blueprint $table_bp) use ($table, $columns) {
                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) continue;

                    // Use char(36) instead of uuid() directly to be more explicit if needed,
                    // but uuid() is fine in Laravel for char(36).
                    $table_bp->string($column, 36)->nullable(true)->change();
                }
            });
        }

        // 4. Regenerate IDs and update foreign keys to valid UUIDs
        // This is necessary because HasUuids trait checks for valid UUID format.
        // Existing integer IDs like '1' will cause 404s.
        $idMappings = [];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;
            if (!in_array('id', $columns)) continue;

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                if (empty($row->id) || !Str::isUuid($row->id)) {
                    $newUuid = (string) Str::uuid();
                    $idMappings[$table][$row->id] = $newUuid;
                    DB::table($table)->where('id', $row->id)->update(['id' => $newUuid]);
                }
            }
        }

        // Update foreign key references
        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) continue;

            foreach ($columns as $column) {
                if ($column === 'id') continue;
                if (!Schema::hasColumn($table, $column)) continue;

                // Determine which table this column likely points to
                $referencedTable = null;
                if (str_ends_with($column, '_id')) {
                    $prefix = substr($column, 0, -3);
                    // Special cases
                    if ($prefix === 'created_by' || $prefix === 'user' || $prefix === 'voter' || $prefix === 'sub_admin') {
                        $referencedTable = 'users';
                    } elseif ($prefix === 'organization') {
                        $referencedTable = 'organizations';
                    } elseif ($prefix === 'election') {
                        $referencedTable = 'elections';
                    } elseif ($prefix === 'partylist') {
                        $referencedTable = 'partylists';
                    } elseif ($prefix === 'position') {
                        $referencedTable = 'positions';
                    } elseif ($prefix === 'candidate') {
                        $referencedTable = 'candidates';
                    }

                    if ($referencedTable && isset($idMappings[$referencedTable])) {
                        foreach ($idMappings[$referencedTable] as $oldId => $newId) {
                            // Use raw update to avoid type issues and ensure quoting
                            DB::statement("UPDATE `{$table}` SET `{$column}` = ? WHERE `{$column}` = ?", [$newId, $oldId]);
                        }
                    }
                }
            }
        }

        // Special case for election_user pivot and other non-standard columns
        if (Schema::hasTable('election_user')) {
             if (isset($idMappings['elections'])) {
                 foreach ($idMappings['elections'] as $oldId => $newId) {
                     DB::table('election_user')->where('election_id', $oldId)->update(['election_id' => $newId]);
                 }
             }
             if (isset($idMappings['users'])) {
                 foreach ($idMappings['users'] as $oldId => $newId) {
                     DB::table('election_user')->where('user_id', $oldId)->update(['user_id' => $newId]);
                 }
             }
        }
    }

    /**
     * Get foreign keys for a table.
     */
    private function getTableForeignKeys(string $table): array
    {
        $foreignKeys = [];
        try {
            $results = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table]);

            foreach ($results as $result) {
                $foreignKeys[] = $result->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // Fallback or ignore if query fails
        }
        return $foreignKeys;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();
    }
};
