<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2); // قيمة الربح
            $table->string('source')->nullable(); // من أين جاء (هتملاه لما تجهز الدفع)
            $table->enum('status', ['available', 'pending_withdrawal', 'withdrawn'])->default('available');
            $table->foreignId('withdrawal_request_id')->nullable()->constrained('withdrawal_requests')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
