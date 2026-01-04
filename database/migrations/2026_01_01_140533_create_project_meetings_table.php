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
        // التعديل في ملف Migration الخاص بالاجتماعات
        Schema::create('project_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained('special_requests')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // من أنشأ الاجتماع
            $table->string('title');
            $table->text('meeting_link')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->timestamps();
        });

        // جدول الربط لتتبع حالة كل شخص
        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_meeting_id')->constrained('project_meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // الحالات: pending (بانتظار الموافقة), accepted (موافق), declined (يعتذر), attended (حضر)
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_meetings');
    }
};
