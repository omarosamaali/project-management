<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'course_subcategory_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('course_subcategory_id');
            });
        }

        Schema::dropIfExists('course_subcategories');
    }

    public function down(): void
    {
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
            $table->foreignId('course_subcategory_id')
                ->nullable()
                ->after('course_category_id')
                ->constrained('course_subcategories')
                ->nullOnDelete();
        });
    }
};
