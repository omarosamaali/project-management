<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'teaching_sample_link')) {
                $table->string('teaching_sample_link', 1000)->nullable()->after('teaching_sample_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'teaching_sample_link')) {
                $table->dropColumn('teaching_sample_link');
            }
        });
    }
};
