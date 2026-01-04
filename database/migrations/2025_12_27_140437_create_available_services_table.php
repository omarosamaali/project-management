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
        Schema::create('available_services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخدمة
            $table->string('slug')->unique(); // معرف نصي
            $table->text('description'); // الوصف
            $table->string('icon'); // الأيقونة (fa-laptop-code)
            $table->boolean('is_active')->default(true); // فعالة أم لا
            $table->integer('order')->default(0); // الترتيب
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_services');
    }
};
