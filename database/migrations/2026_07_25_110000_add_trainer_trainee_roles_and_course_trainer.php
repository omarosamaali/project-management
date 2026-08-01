<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand users.role enum with trainer + trainee
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'partner',
            'client',
            'design_partner',
            'advertising_partner',
            'independent_partner',
            'trainer',
            'trainee'
        ) NOT NULL DEFAULT 'client'");

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'trainer_id')) {
                $table->foreignId('trainer_id')
                    ->nullable()
                    ->after('service_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'trainer_id')) {
                $table->dropConstrainedForeignId('trainer_id');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin',
            'partner',
            'client',
            'design_partner',
            'advertising_partner',
            'independent_partner'
        ) NOT NULL DEFAULT 'client'");
    }
};
