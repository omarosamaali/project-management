<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_meetings', function (Blueprint $table) {
            // 1. جعل الحقل القديم nullable لكي لا يسبب خطأ عند إنشاء اجتماع لطلب عادي
            $table->foreignId('special_request_id')->nullable()->change();

            // 2. إضافة حقل الطلب العادي (الذي سبب لك المشكلة)
            // تأكد أن اسم الجدول هو 'requests' أو غيره حسب ما هو موجود عندك
            $table->foreignId('request_id')
                ->after('special_request_id') // ترتيبه في الجدول
                ->nullable()
                ->constrained('requests') // اسم جدول الطلبات العادية
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('project_meetings', function (Blueprint $table) {
            // تراجع عن التعديلات في حال عمل Rollback
            $table->dropForeign(['request_id']);
            $table->dropColumn('request_id');
            $table->foreignId('special_request_id')->nullable(false)->change();
        });
    }
};
