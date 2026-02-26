<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('status');
        });

        // ملء delivered_at للسجلات الموجودة التي حالتها closed
        DB::statement("
            UPDATE requests
            SET delivered_at = updated_at
            WHERE status = 'closed' AND delivered_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });
    }
};
