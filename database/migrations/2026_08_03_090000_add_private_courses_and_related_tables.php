<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'allows_private_requests')) {
                $table->boolean('allows_private_requests')->default(false)->after('status');
            }
            if (! Schema::hasColumn('courses', 'private_course_price')) {
                $table->decimal('private_course_price', 10, 2)->nullable()->after('allows_private_requests');
            }
            if (! Schema::hasColumn('courses', 'private_of_course_id')) {
                $table->foreignId('private_of_course_id')->nullable()->after('private_course_price')
                    ->constrained('courses')->nullOnDelete();
            }
            if (! Schema::hasColumn('courses', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('private_of_course_id');
            }
            if (! Schema::hasColumn('courses', 'cancel_reason')) {
                $table->string('cancel_reason')->nullable()->after('canceled_at');
            }
        });

        Schema::create('private_course_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('trainee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('private_price', 10, 2);
            $table->string('status', 40)->default('pending_trainer')->index();
            $table->timestamp('proposed_start_at')->nullable();
            $table->timestamp('proposed_end_at')->nullable();
            $table->timestamp('dates_accepted_by_trainer_at')->nullable();
            $table->timestamp('dates_accepted_by_trainee_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('block_reason')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->timestamp('payment_due_at')->nullable();
            $table->unsignedBigInteger('private_course_id')->nullable()->index();
            $table->timestamp('trainer_responded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('trainee_note')->nullable();
            $table->timestamps();

            $table->index(['trainee_id', 'status']);
            $table->index(['trainer_id', 'status']);
            $table->index(['source_course_id', 'status']);
        });

        Schema::table('private_course_requests', function (Blueprint $table) {
            $table->foreign('private_course_id')->references('id')->on('courses')->nullOnDelete();
        });

        Schema::create('private_course_request_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_course_request_id')->constrained('private_course_requests')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 60);
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('private_course_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_course_request_id')->constrained('private_course_requests')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('trainee_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('AED');
            $table->string('status', 40)->default('required')->index();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('screenshot_path')->nullable();
            $table->timestamp('screenshot_uploaded_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('trainee_confirmed_at')->nullable();
            $table->timestamp('trainee_confirm_due_at')->nullable();
            $table->timestamps();
        });

        Schema::create('course_special_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path');
            $table->timestamps();

            $table->unique('payment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_special_certificates');
        Schema::dropIfExists('private_course_refunds');
        Schema::dropIfExists('private_course_request_events');
        Schema::dropIfExists('private_course_requests');

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'private_of_course_id')) {
                $table->dropConstrainedForeignId('private_of_course_id');
            }
            foreach (['cancel_reason', 'canceled_at', 'private_course_price', 'allows_private_requests'] as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
