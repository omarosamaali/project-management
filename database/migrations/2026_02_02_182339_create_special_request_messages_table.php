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
        Schema::create('special_request_messages', function (Blueprint $table) {
            $table->id();
            // الربط مع الطلب الخاص
            $table->foreignId('special_request_id')->constrained('special_requests')->onDelete('cascade');
            // الربط مع المستخدم (اللي بعت الرسالة)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // محتوى الرسالة
            $table->text('message');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_request_messages');
    }
};
