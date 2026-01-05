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
        Schema::table('elections', function (Blueprint $table) {
            if (!Schema::hasColumn('elections', 'accepted_domains')) {
                $table->text('accepted_domains')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('elections', 'max_votes')) {
                $table->integer('max_votes')->default(1)->after('end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('elections', 'accepted_domains')) {
                $columns[] = 'accepted_domains';
            }
            if (Schema::hasColumn('elections', 'max_votes')) {
                $columns[] = 'max_votes';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
