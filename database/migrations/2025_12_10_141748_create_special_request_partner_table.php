<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_request_partner', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->foreignId('special_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('request_id')->nullable()->constrained('requests')->onDelete('cascade');            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('profit_share_percentage')->nullable();
            $table->enum('status', ['جديد', 'تحت الاجراء', 'بإنتظار رد العميل', 'بالانتظار', 'ملغية', 'معلقة', 'منتهية'])->default('جديد');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['special_request_id', 'partner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_request_partner');
    }
};
