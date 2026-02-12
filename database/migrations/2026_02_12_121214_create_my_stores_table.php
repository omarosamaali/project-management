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
        Schema::create('my_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('system_external')->default(true);
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->integer('execution_days');
            $table->integer('support_days')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en');
            $table->json('requirements');
            $table->json('features');
            $table->json('buttons')->nullable();
            $table->string('main_image');
            $table->json('images')->nullable();
            $table->string('status')->default('قيد المراجعة');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_stores');
    }
};
