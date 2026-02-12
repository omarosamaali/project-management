<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            // 1. جعل الحقل القديم nullable
            $table->foreignId('special_request_id')->nullable()->change();

            // 2. إضافة الحقل الجديد الخاص بالطلبات العادية
            $table->foreignId('request_id')->nullable()->after('id')->constrained('requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
            $table->dropColumn('request_id');
            $table->foreignId('special_request_id')->nullable(false)->change();
        });
    }
};
