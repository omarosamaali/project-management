<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_meetings', function (Blueprint $table) {
            $table->string('location')->nullable()->after('meeting_type');
        });
    }

    public function down(): void
    {
        Schema::table('project_meetings', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
