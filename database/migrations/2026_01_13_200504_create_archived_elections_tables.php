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
        Schema::create('archived_elections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('original_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status');
            $table->uuid('organization_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->json('settings')->nullable();
            $table->json('results_summary')->nullable();
            $table->timestamp('archived_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('archived_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('archived_election_id');
            $table->uuid('original_vote_id');
            $table->uuid('candidate_id');
            $table->uuid('voter_id');
            $table->uuid('position_id');
            $table->string('ip_address')->nullable();
            $table->timestamp('voted_at');
            $table->timestamps();

            $table->foreign('archived_election_id')->references('id')->on('archived_elections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_votes');
        Schema::dropIfExists('archived_elections');
    }
};
