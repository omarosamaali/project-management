<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'last_path_item_id')) {
                $table->unsignedBigInteger('last_path_item_id')->nullable()->after('course_id');
                $table->index('last_path_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'last_path_item_id')) {
                $table->dropIndex(['last_path_item_id']);
                $table->dropColumn('last_path_item_id');
            }
        });
    }
};
