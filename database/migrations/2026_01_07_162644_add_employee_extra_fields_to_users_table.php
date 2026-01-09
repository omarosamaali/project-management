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
        Schema::table('users', function (Blueprint $table) {
            // --- نظام ساعات العمل والدوام (مأخوذة من الـ HTML) ---
            $table->string('salary_currency')->nullable()->change();
            $table->decimal('salary_amount', 10, 2)->nullable()->change();
            $table->date('hiring_date')->nullable()->change();

            $table->string('work_start_time')->nullable();      // ساعة بداية العمل
            $table->string('work_end_time')->nullable();        // ساعة نهاية العمل
            $table->decimal('daily_work_hours', 5, 2)->nullable(); // عدد ساعات العمل
            $table->integer('break_minutes')->nullable();       // ساعات الاستراحة بالدقائق
            $table->decimal('overtime_hourly_rate', 10, 2)->nullable(); // قيمة العمل الإضافي بالساعة

            // --- الخصومات والمدد المسموحة ---
            $table->integer('allowed_late_minutes')->nullable();    // المدة المسموح بها للتأخير
            $table->decimal('morning_late_deduction', 10, 2)->nullable(); // خصم التأخير الصباحي
            $table->decimal('break_late_deduction', 10, 2)->nullable();   // خصم التأخير من الاستراحة
            $table->decimal('early_leave_deduction', 10, 2)->nullable();  // خصم الخروج المبكر
            $table->string('first_country')->nullable();    // الدولة الأولى
            // --- أيام الإجازة الأسبوعية ---
            $table->json('vacation_days')->nullable(); // لتخزين مصفوفة الأيام (السبت، الأحد.. إلخ)

            // --- حقول نظام حساب الراتب (Scale) المذكورة في أسفل الـ HTML ---
            $table->decimal('salary_amount_scale', 10, 2)->nullable();
            $table->string('salary_currency_scale')->nullable();
            $table->date('hiring_date_scale')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
