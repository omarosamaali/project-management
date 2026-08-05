<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('trainer_off_days')) {
            Schema::create('trainer_off_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('date');
                $table->string('note')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'date']);
            });
        }

        if (! Schema::hasTable('course_path_lesson_links')) {
            Schema::create('course_path_lesson_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('path_item_id')->constrained('course_path_items')->cascadeOnDelete();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->string('url', 1000);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_path_lesson_links');
        Schema::dropIfExists('trainer_off_days');
    }
};
