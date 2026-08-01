<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_category_id')->constrained('course_categories')->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('course_category_id')
                ->nullable()
                ->after('service_id')
                ->constrained('course_categories')
                ->nullOnDelete();
            $table->foreignId('course_subcategory_id')
                ->nullable()
                ->after('course_category_id')
                ->constrained('course_subcategories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_subcategory_id');
            $table->dropConstrainedForeignId('course_category_id');
        });

        Schema::dropIfExists('course_subcategories');
        Schema::dropIfExists('course_categories');
    }
};
