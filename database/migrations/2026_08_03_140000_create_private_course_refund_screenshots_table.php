<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recreate if a previous attempt left a broken table without FKs.
        if (Schema::hasTable('private_course_refund_screenshots')) {
            $hasFk = false;
            try {
                $fks = DB::select('
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.TABLE_CONSTRAINTS
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = ?
                      AND CONSTRAINT_TYPE = ?
                ', ['private_course_refund_screenshots', 'FOREIGN KEY']);
                $hasFk = count($fks) > 0;
            } catch (\Throwable) {
                $hasFk = false;
            }

            if (! $hasFk) {
                Schema::drop('private_course_refund_screenshots');
            }
        }

        if (! Schema::hasTable('private_course_refund_screenshots')) {
            Schema::create('private_course_refund_screenshots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('private_course_refund_id')->index();
                $table->string('path');
                $table->string('kind', 20)->default('pending')->index(); // pending|success|fail
                $table->text('note')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('private_course_refund_id', 'pcrs_refund_fk')
                    ->references('id')
                    ->on('private_course_refunds')
                    ->cascadeOnDelete();

                $table->foreign('uploaded_by', 'pcrs_uploader_fk')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('private_course_refunds') && Schema::hasTable('private_course_refund_screenshots')) {
            $rows = DB::table('private_course_refunds')
                ->whereNotNull('screenshot_path')
                ->where('screenshot_path', '!=', '')
                ->get(['id', 'screenshot_path', 'admin_note', 'admin_id', 'screenshot_uploaded_at', 'created_at', 'updated_at']);

            foreach ($rows as $row) {
                $exists = DB::table('private_course_refund_screenshots')
                    ->where('private_course_refund_id', $row->id)
                    ->where('path', $row->screenshot_path)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('private_course_refund_screenshots')->insert([
                    'private_course_refund_id' => $row->id,
                    'path' => $row->screenshot_path,
                    'kind' => 'pending',
                    'note' => $row->admin_note,
                    'uploaded_by' => $row->admin_id,
                    'created_at' => $row->screenshot_uploaded_at ?: ($row->created_at ?: now()),
                    'updated_at' => $row->updated_at ?: now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('private_course_refund_screenshots');
    }
};
