<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('embedded_meeting_id')->nullable()->after('online_link');
            $table->string('embedded_meeting_status', 32)->nullable()->after('embedded_meeting_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['embedded_meeting_id', 'embedded_meeting_status']);
        });
    }
};
