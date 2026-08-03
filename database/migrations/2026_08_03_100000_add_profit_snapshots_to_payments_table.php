<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            $defaults = [
                'academy_trainer_profit_online' => '60',
                'academy_trainer_profit_recorded' => '50',
                'academy_trainer_profit_private' => '70',
            ];
            foreach ($defaults as $key => $value) {
                if (! DB::table('settings')->where('key', $key)->exists()) {
                    DB::table('settings')->insert([
                        'key' => $key,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'trainer_profit_pct')) {
                $table->decimal('trainer_profit_pct', 8, 2)->nullable()->after('original_price');
            }
            if (! Schema::hasColumn('payments', 'trainer_profit_amount')) {
                $table->decimal('trainer_profit_amount', 12, 2)->nullable()->after('trainer_profit_pct');
            }
            if (! Schema::hasColumn('payments', 'platform_profit_amount')) {
                $table->decimal('platform_profit_amount', 12, 2)->nullable()->after('trainer_profit_amount');
            }
            if (! Schema::hasColumn('payments', 'private_course_request_id')) {
                $table->foreignId('private_course_request_id')->nullable()->after('course_id')
                    ->constrained('private_course_requests')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'private_course_request_id')) {
                $table->dropConstrainedForeignId('private_course_request_id');
            }
            foreach (['platform_profit_amount', 'trainer_profit_amount', 'trainer_profit_pct'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
