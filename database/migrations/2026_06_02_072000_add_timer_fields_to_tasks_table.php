<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('tracked_seconds')->default(0)->after('status');
            $table->timestamp('timer_started_at')->nullable()->after('tracked_seconds');
            $table->boolean('is_timer_running')->default(false)->after('timer_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['tracked_seconds', 'timer_started_at', 'is_timer_running']);
        });
    }
};

