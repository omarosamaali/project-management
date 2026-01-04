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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // system_id nullable عشان الدفعات الخاصة أو الجزئية مش ليها نظام
            $table->foreignId('system_id')->nullable()->constrained()->onDelete('cascade');

            // للطلبات الخاصة (كاملة أو جزئية)
            $table->foreignId('special_request_id')->nullable()->constrained('special_requests')->onDelete('cascade');

            // للدفعات الجزئية فقط (installments)
            $table->foreignId('request_payment_id')->nullable()->constrained('request_payments')->onDelete('cascade');

            // من Ziina
            $table->string('payment_id')->nullable(); // payment intent ID من Ziina

            $table->decimal('amount', 12, 2);          // المبلغ الإجمالي (مع الرسوم)
            $table->decimal('original_price', 12, 2);   // السعر الأساسي بدون رسوم
            $table->decimal('fees', 10, 2)->default(0);

            $table->string('status')->default('pending'); // pending, completed, failed, etc.
            $table->string('payment_method')->default('ziina');
            $table->string('currency', 3)->default('AED'); // AED أو غيره لو هتوسع

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
