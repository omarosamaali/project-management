<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project__managers', function (Blueprint $table) {
            // تعديل الأعمدة لتصبح nullable
            $table->foreignId('special_request_id')->nullable()->change();
            $table->foreignId('request_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project__managers', function (Blueprint $table) {
            $table->foreignId('special_request_id')->nullable(false)->change();
            $table->foreignId('request_id')->nullable(false)->change();
        });
    }
};
