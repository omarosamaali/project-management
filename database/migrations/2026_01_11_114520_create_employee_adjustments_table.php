<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('employee_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ربط بالموظف
            $table->enum('type', ['bonus', 'deduction']); // نوع العملية: مكافأة أو خصم
            $table->decimal('amount', 10, 2); // المبلغ
            $table->date('date'); // التاريخ
            $table->text('notes')->nullable(); // الملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_adjustments');
    }
};
