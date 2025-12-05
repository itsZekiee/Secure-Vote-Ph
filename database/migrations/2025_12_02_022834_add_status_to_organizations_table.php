<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('active');
                $table->timestamps();
            });

            return;
        }

        if (!Schema::hasColumn('organizations', 'status')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->string('status')->default('active')->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('organizations', 'status')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
