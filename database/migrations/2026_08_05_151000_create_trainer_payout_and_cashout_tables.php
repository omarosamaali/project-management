<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payout_methods')) {
            Schema::create('payout_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_system')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payout_method_fields')) {
            Schema::create('payout_method_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payout_method_id');
                $table->string('key');
                $table->string('label_ar');
                $table->string('label_en');
                $table->string('input_type')->default('text');
                $table->boolean('is_required')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('payout_method_id', 'pmf_method_fk')
                    ->references('id')
                    ->on('payout_methods')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('trainer_payment_profiles')) {
            Schema::create('trainer_payment_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->unsignedBigInteger('payout_method_id')->nullable();
                $table->json('field_values')->nullable();
                $table->string('bank_account_name')->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_country')->nullable();
                $table->string('id_card_front_path')->nullable();
                $table->string('id_card_back_path')->nullable();
                $table->timestamp('configured_at')->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id', 'tpp_user_fk')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

                $table->foreign('payout_method_id', 'tpp_method_fk')
                    ->references('id')
                    ->on('payout_methods')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('trainer_cashout_requests')) {
            Schema::create('trainer_cashout_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('AED');
                $table->string('status', 30)->default('pending_admin');
                $table->unsignedBigInteger('payout_method_id')->nullable();
                $table->json('payout_snapshot')->nullable();
                $table->decimal('available_balance_snapshot', 12, 2)->nullable();
                $table->timestamp('trainer_confirm_due_at')->nullable();
                $table->timestamp('trainer_confirmed_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();

                $table->foreign('user_id', 'tcr_user_fk')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

                $table->foreign('payout_method_id', 'tcr_method_fk')
                    ->references('id')
                    ->on('payout_methods')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('trainer_cashout_screenshots')) {
            Schema::create('trainer_cashout_screenshots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('trainer_cashout_request_id');
                $table->string('kind', 20)->default('pending');
                $table->string('path');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();

                $table->foreign('trainer_cashout_request_id', 'tcrs_request_fk')
                    ->references('id')
                    ->on('trainer_cashout_requests')
                    ->cascadeOnDelete();

                $table->foreign('uploaded_by', 'tcrs_uploader_fk')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }

        if (! DB::table('payout_methods')->where('is_system', true)->exists()) {
            DB::table('payout_methods')->insert([
                'name_ar' => 'تحويل بنكي',
                'name_en' => 'Bank transfer',
                'image_path' => null,
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_cashout_screenshots');
        Schema::dropIfExists('trainer_cashout_requests');
        Schema::dropIfExists('trainer_payment_profiles');
        Schema::dropIfExists('payout_method_fields');
        Schema::dropIfExists('payout_methods');
    }
};
