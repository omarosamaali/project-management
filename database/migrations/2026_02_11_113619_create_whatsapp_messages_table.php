<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->index();               // رقم الواتساب اللي اتبعتله
            $template = $table->string('template')->nullable();     // اسم القالب (trabar, otp, ...)
            $table->string('type')->default('outgoing');    // outgoing / incoming (لو هتدعم الردود لاحقًا)
            $table->text('message_content')->nullable();   // النص الفعلي أو وصف الرسالة
            $table->json('payload')->nullable();            // الـ params اللي اتبعتت (اختياري)
            $table->string('message_id')->nullable();       // wamid... من رد 4jawaly
            $table->string('status')->default('pending');   // pending, sent, delivered, failed, read
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};