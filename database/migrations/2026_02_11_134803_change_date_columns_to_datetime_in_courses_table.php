<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // تغيير نوع الحقول من date إلى dateTime
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
            $table->dateTime('last_date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // للرجوع للحالة السابقة (اختياري)
            $table->date('start_date')->change();
            $table->date('end_date')->change();
            $table->date('last_date')->change();
        });
    }
};
