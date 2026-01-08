<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('voters', 'first_name')) {
            Schema::table('voters', function (Blueprint $table) {
                $table->string('first_name')->nullable()->after('voter_id');
                $table->string('middle_name')->nullable()->after('first_name');
                $table->string('last_name')->nullable()->after('middle_name');
            });

            // Backfill from `name` column if present
            try {
                $voters = DB::table('voters')->select('id', 'name')->get();
                foreach ($voters as $v) {
                    $full = trim($v->name ?? '');
                    if ($full === '') continue;
                    $parts = preg_split('/\s+/', $full);
                    if (count($parts) === 1) {
                        $first = $parts[0];
                        $middle = null;
                        $last = $parts[0];
                    } elseif (count($parts) === 2) {
                        $first = $parts[0];
                        $middle = null;
                        $last = $parts[1];
                    } else {
                        $first = array_shift($parts);
                        $last = array_pop($parts);
                        $middle = implode(' ', $parts);
                    }

                    DB::table('voters')->where('id', $v->id)->update([
                        'first_name' => $first,
                        'middle_name' => $middle,
                        'last_name' => $last,
                    ]);
                }
            } catch (\Throwable $e) {
                // ignore backfill errors
            }
        }
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            if (Schema::hasColumn('voters', 'first_name')) $table->dropColumn('first_name');
            if (Schema::hasColumn('voters', 'middle_name')) $table->dropColumn('middle_name');
            if (Schema::hasColumn('voters', 'last_name')) $table->dropColumn('last_name');
        });
    }
};
