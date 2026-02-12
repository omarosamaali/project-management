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
        Schema::create('request_price', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول المشاريع أو المشاريع
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');

            $table->string('payment_name'); // اسم الدفعة (مثلاً: الدفعة الأولى)
            $table->decimal('amount', 15, 4); // المبلغ بدقة 4 أرقام عشرية كما في قاعدة بياناتك
            $table->date('due_date')->nullable(); // تاريخ الاستحقاق
            $table->enum('status', ['unpaid', 'pending', 'paid', 'failed'])->default('unpaid'); // الحالة
            $table->timestamp('paid_at')->nullable(); // تاريخ الدفع الفعلي
            $table->text('notes')->nullable(); // ملاحظات

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_price');
    }
};
