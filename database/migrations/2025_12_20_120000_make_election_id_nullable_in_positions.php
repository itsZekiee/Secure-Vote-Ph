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
        // Make election_id nullable to allow positions without an election
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('positions', function (Blueprint $table) {
                $table->unsignedBigInteger('election_id')->nullable()->change();
            });
        } else {
            DB::statement('ALTER TABLE `positions` MODIFY `election_id` BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make election_id NOT NULL again
        DB::statement('ALTER TABLE `positions` MODIFY `election_id` BIGINT UNSIGNED NOT NULL');
    }
};
