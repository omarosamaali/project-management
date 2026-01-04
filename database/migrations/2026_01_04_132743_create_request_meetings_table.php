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
        Schema::create('request_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade'); // الربط مع الجدول العادي
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('meeting_link')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->timestamps();
        });

        // جدول المشاركين (يمكنك استخدام نفس الجدول القديم أو إنشاء واحد جديد للطلبات العادية)
        Schema::create('request_meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_meeting_id')->constrained('request_meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_meetings');
    }
};
