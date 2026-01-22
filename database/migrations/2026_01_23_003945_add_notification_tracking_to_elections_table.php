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
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('opened_notification_sent_at')->nullable();
            $table->timestamp('closed_notification_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'opened_notification_sent_at', 'closed_notification_sent_at']);
        });
    }
};
