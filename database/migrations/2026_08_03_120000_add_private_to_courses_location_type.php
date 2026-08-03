<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'location_type')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY location_type ENUM('online','on_site','recorded','private') NOT NULL DEFAULT 'online'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'location_type')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::table('courses')->where('location_type', 'private')->update(['location_type' => 'online']);
            DB::statement("ALTER TABLE courses MODIFY location_type ENUM('online','on_site','recorded') NOT NULL DEFAULT 'online'");
        }
    }
};
