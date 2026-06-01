<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_request_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained('special_requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['special_request_id', 'user_id']);
        });

        // نقل العميل الأصلي (user_id) إلى جدول الـ pivot
        DB::statement('
            INSERT IGNORE INTO special_request_clients (special_request_id, user_id, created_at, updated_at)
            SELECT id, user_id, NOW(), NOW()
            FROM special_requests
            WHERE user_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('special_request_clients');
    }
};
