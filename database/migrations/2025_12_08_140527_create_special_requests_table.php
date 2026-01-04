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
        Schema::create('special_requests', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('project_type');
            $table->longText('description');
            $table->longText('core_features');
            $table->string('examples')->nullable();
            $table->string('budget')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamp('bidding_deadline')->nullable();
            $table->enum('status', [
                'pending',
                'in_review',
                'in_progress',
                'completed',
                'canceled',
                'بانتظار الدفع'
            ])->default('pending');
            $table->boolean('is_project')->default(false);
            $table->integer('price')->nullable();
            $table->enum('payment_type', ['single', 'installments'])->default('single');
            $table->json('installments_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_requests');
    }
};
