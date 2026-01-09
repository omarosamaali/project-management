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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('overtime_value', 10, 2)->default(0); // قيمة الإضافي
            $table->decimal('deduction_value', 10, 2)->default(0); // الخصم
            $table->decimal('carried_forward', 10, 2)->default(0); // المترحل من الشهر السابق
            $table->decimal('total_due', 10, 2); // الراتب المستحق النهائي
            $table->string('attachment')->nullable(); // الصورة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
