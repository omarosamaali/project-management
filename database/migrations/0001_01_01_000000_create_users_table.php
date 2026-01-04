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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'partner', 'client', 'design_partner', 'advertising_partner', 'independent_partner'])->default('client');
            $table->enum('status', ['active', 'inactive', 'pending', 'blocked'])->default('active')->nullable();

            // --- بيانات الملف الشخصي والصور ---
            $table->string('profile_photo_path')->nullable();
            $table->text('skills')->nullable();
            $table->string('id_card_path')->nullable();
            $table->string('verification_video')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();

            // --- الصلاحيات والخيارات (Boolean) ---
            $table->boolean('is_employee')->default(false);
            $table->boolean('services_screen_available')->default(false);
            $table->boolean('can_view_projects')->default(false);
            $table->boolean('can_view_notes')->default(false);
            $table->boolean('can_propose_quotes')->default(false);
            $table->boolean('can_enter_knowledge_bank')->default(false);
            $table->boolean('apply_working_hours')->default(false);
            $table->boolean('can_request_meetings')->default(false);

            // --- القسم الأول: الملاحظة الإدارية (التي سألت عنها) ---
            $table->string('note_title')->nullable();        // عنوان الملاحظة
            $table->date('note_date')->nullable();          // تاريخ الملاحظة
            $table->text('note_details')->nullable();       // التفاصيل
            $table->string('note_attachment')->nullable();   // المرفق
            $table->boolean('is_visible_to_employee')->default(false); // رؤية الموظف

            // --- القسم الثاني: دليل الرواتب (الذي سألت عنه) ---
            $table->boolean('apply_salary_scale')->default(false); // سويتش تفعيل الراتب
            $table->integer('salary_year')->nullable();            // السنة
            $table->integer('salary_month')->nullable();           // الشهر
            $table->string('salary_attachment')->nullable();       // مستند الراتب
            $table->text('salary_notes')->nullable();              // ملاحظات الراتب
            $table->decimal('salary_amount', 10, 2)->nullable();   // الراتب الأساسي
            $table->string('salary_currency', 10)->default('EGP'); // العملة
            $table->date('hiring_date')->nullable();               // تاريخ التعيين

            // --- بيانات مالية إضافية ---
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('orders')->nullable();
            $table->enum('withdrawal_method', ['wallet', 'paypal'])->nullable();
            $table->string('withdrawal_email')->nullable();
            $table->text('withdrawal_notes')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
