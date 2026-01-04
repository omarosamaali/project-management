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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('response_speed')->default(0);
            $table->decimal('execution_time', 5, 2)->default(0); // بالأيام
            $table->integer('message_response_rate')->default(0); // نسبة مئوية 0-100
            $table->integer('support_tickets_closed')->default(0); // عدد التذاكر المغلقة
            $table->integer('completed_tasks')->default(0); // عدد المهام المكتملة
            $table->date('performance_date'); // تاريخ التسجيل

            $table->timestamps();

            $table->index(['user_id', 'performance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
