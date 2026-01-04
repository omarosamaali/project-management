<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained('special_requests')->onDelete('cascade');
            $table->string('payment_name');
            $table->decimal('amount', 10, 4);
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_payments');
    }
};
