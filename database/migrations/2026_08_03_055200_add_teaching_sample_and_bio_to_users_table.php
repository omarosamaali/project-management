<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'teaching_sample_path')) {
                $table->string('teaching_sample_path')->nullable()->after('resume_path');
            }
            if (! Schema::hasColumn('users', 'trainer_bio')) {
                $table->text('trainer_bio')->nullable()->after('teaching_sample_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'teaching_sample_path')) {
                $table->dropColumn('teaching_sample_path');
            }
            if (Schema::hasColumn('users', 'trainer_bio')) {
                $table->dropColumn('trainer_bio');
            }
        });
    }
};
