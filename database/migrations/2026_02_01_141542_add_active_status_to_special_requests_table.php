<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'in_review',
                'active',
                'in_progress',
                'completed',
                'canceled',
                'بانتظار الدفع',
                'بانتظار عروض الاسعار',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'in_review',
                'active',
                'in_progress',
                'completed',
                'canceled',
                'بانتظار الدفع',
                'بانتظار عروض الاسعار',
            ])->default('pending')->change();
        });
    }
};
