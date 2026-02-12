<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_activities', function (Blueprint $table) {
            $table->foreignId('special_request_id')->nullable()->change();
            $table->foreignId('request_id')
                  ->after('special_request_id')
                  ->nullable()
                  ->constrained('requests') // تأكد أن اسم الجدول هو 'requests'
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_activities', function (Blueprint $table) {
            //
        });
    }
};
