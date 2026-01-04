<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_request_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained('special_requests')->onDelete('cascade');
            $table->string('payment_name'); // اسم الدفعة (مثل: الدفعة الأولى)
            $table->decimal('amount', 10, 2); // المبلغ
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable(); // تاريخ الدفع
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_request_payments');
    }
};
