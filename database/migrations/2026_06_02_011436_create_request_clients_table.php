<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['request_id', 'user_id']);
        });

        // نقل العميل الأصلي (client_id) إلى جدول الـ pivot
        DB::statement('
            INSERT IGNORE INTO request_clients (request_id, user_id, created_at, updated_at)
            SELECT id, client_id, NOW(), NOW()
            FROM requests
            WHERE client_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('request_clients');
    }
};
