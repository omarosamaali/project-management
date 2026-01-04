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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained('special_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // منظم الاجتماع
            $table->string('title');

            // التعديل المهم هنا:
            $table->json('attendees'); // لتخزين مصفوفة الأسماء التي اخترناها
            $table->string('meeting_link')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // اختيارية: إذا كنت تريد تخزين الحالة يدوياً، لكننا سنعتمد على "الوقت" برمجياً أفضل
            // $table->string('status')->default('بالانتظار'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
