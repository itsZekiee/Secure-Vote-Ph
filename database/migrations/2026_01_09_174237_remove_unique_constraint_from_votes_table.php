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
        Schema::table('votes', function (Blueprint $table) {
            // Add separate indexes for columns that were part of the unique index
            // so that foreign key constraints are still satisfied
            $table->index('election_id');
            $table->index('voter_id');
            $table->index('position_id');

            $table->dropUnique(['election_id', 'voter_id', 'position_id']);
            $table->string('ballot_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->unique(['election_id', 'voter_id', 'position_id']);
            $table->dropColumn('ballot_id');

            $table->dropIndex(['election_id']);
            $table->dropIndex(['voter_id']);
            $table->dropIndex(['position_id']);
        });
    }
};
