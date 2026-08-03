<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY status ENUM('active','inactive','canceled') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::table('courses')->where('status', 'canceled')->update(['status' => 'inactive']);
            DB::statement("ALTER TABLE courses MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        }
    }
};
