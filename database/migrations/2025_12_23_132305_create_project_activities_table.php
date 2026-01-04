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
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // من قام بالنشاط
            $table->string('type'); // نوع النشاط: (invoice, status_change, file_upload, etc)
            $table->decimal('hours_count', 8, 2)->default(0);
            $table->string('description'); // وصف النشاط بالعربي
            $table->json('properties')->nullable(); // بيانات إضافية إذا احتجت
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_activities');
    }
};
