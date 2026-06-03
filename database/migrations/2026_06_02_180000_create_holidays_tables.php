<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['general', 'private']);
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('salary_deduction_status', ['paid', 'unpaid'])->default('paid');
            $table->text('details')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('holiday_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_id')->constrained('holidays')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['holiday_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_user');
        Schema::dropIfExists('holidays');
    }
};
