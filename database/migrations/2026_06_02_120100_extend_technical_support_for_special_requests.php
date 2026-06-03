<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technical_support', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_support', 'special_request_id')) {
                $table->foreignId('special_request_id')->nullable()->after('request_id')
                    ->constrained('special_requests')->nullOnDelete();
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE technical_support MODIFY request_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE technical_support MODIFY system_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        Schema::table('technical_support', function (Blueprint $table) {
            if (Schema::hasColumn('technical_support', 'special_request_id')) {
                $table->dropForeign(['special_request_id']);
                $table->dropColumn('special_request_id');
            }
        });
    }
};
