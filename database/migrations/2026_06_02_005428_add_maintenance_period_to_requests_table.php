<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->unsignedInteger('maintenance_period')->nullable()->after('status')
                ->comment('عدد وحدات فترة الصيانة بعد التسليم');
            $table->enum('maintenance_unit', ['days', 'months'])->nullable()->after('maintenance_period')
                ->comment('وحدة فترة الصيانة: أيام أو أشهر');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['maintenance_period', 'maintenance_unit']);
        });
    }
};
