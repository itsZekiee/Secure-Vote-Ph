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
        Schema::table('partylists', function (Blueprint $table) {
            if (! Schema::hasColumn('partylists', 'organization_id')) {
                // Add nullable foreign key to organizations.id, set null on delete
                $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partylists', function (Blueprint $table) {
            if (Schema::hasColumn('partylists', 'organization_id')) {
                // dropConstrainedForeignId will remove the foreign key and the column
                $table->dropConstrainedForeignId('organization_id');
            }
        });
    }
};
