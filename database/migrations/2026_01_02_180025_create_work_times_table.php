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
        Schema::create('work_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // اسم الموظف مرتبط بجدول المستخدمين
            $table->string('country'); // بلد المستخدم
            $table->enum('type', ['حضور', 'انصراف', 'خروج للاستراحة', 'دخول من الاستراحة']);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('work_days')->nullable(); // أيام العمل
            $table->string('timezone')->default('UTC'); // لتخزين توقيت بلد المستخدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_times');
    }
};
