<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_units', function (Blueprint $table) {
            if (!Schema::hasColumn('course_units', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('course_id');
            }
            if (!Schema::hasColumn('course_units', 'title_en')) {
                $table->string('title_en')->nullable()->after('title_ar');
            }
        });

        Schema::table('course_path_items', function (Blueprint $table) {
            if (!Schema::hasColumn('course_path_items', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('type');
            }
            if (!Schema::hasColumn('course_path_items', 'title_en')) {
                $table->string('title_en')->nullable()->after('title_ar');
            }
        });

        if (Schema::hasColumn('course_units', 'title')) {
            DB::table('course_units')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('course_units')->where('id', $row->id)->update([
                        'title_ar' => $row->title_ar ?: $row->title,
                        'title_en' => $row->title_en ?: $row->title,
                    ]);
                }
            });
            Schema::table('course_units', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }

        if (Schema::hasColumn('course_path_items', 'title')) {
            DB::table('course_path_items')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('course_path_items')->where('id', $row->id)->update([
                        'title_ar' => $row->title_ar ?: $row->title,
                        'title_en' => $row->title_en ?: $row->title,
                    ]);
                }
            });
            Schema::table('course_path_items', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('course_units', function (Blueprint $table) {
            if (!Schema::hasColumn('course_units', 'title')) {
                $table->string('title')->nullable()->after('course_id');
            }
        });
        if (Schema::hasColumn('course_units', 'title_ar')) {
            DB::table('course_units')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('course_units')->where('id', $row->id)->update([
                        'title' => $row->title_ar ?: $row->title_en ?: '',
                    ]);
                }
            });
            Schema::table('course_units', function (Blueprint $table) {
                $table->dropColumn(['title_ar', 'title_en']);
            });
        }

        Schema::table('course_path_items', function (Blueprint $table) {
            if (!Schema::hasColumn('course_path_items', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
        });
        if (Schema::hasColumn('course_path_items', 'title_ar')) {
            DB::table('course_path_items')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('course_path_items')->where('id', $row->id)->update([
                        'title' => $row->title_ar ?: $row->title_en ?: '',
                    ]);
                }
            });
            Schema::table('course_path_items', function (Blueprint $table) {
                $table->dropColumn(['title_ar', 'title_en']);
            });
        }
    }
};
