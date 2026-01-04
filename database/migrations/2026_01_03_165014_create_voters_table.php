<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('voters');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('voter_id')->nullable()->unique();
            $table->string('password');
            $table->foreignId('election_id')->nullable()->constrained('elections')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('voters');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
