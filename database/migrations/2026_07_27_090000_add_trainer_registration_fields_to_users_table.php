<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'course_category_id')) {
                $table->foreignId('course_category_id')
                    ->nullable()
                    ->after('avatar')
                    ->constrained('course_categories')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'id_card_front_path')) {
                $table->string('id_card_front_path')->nullable()->after('id_card_path');
            }

            if (! Schema::hasColumn('users', 'id_card_back_path')) {
                $table->string('id_card_back_path')->nullable()->after('id_card_front_path');
            }

            if (! Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('id_card_back_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'course_category_id')) {
                $table->dropConstrainedForeignId('course_category_id');
            }
            if (Schema::hasColumn('users', 'id_card_front_path')) {
                $table->dropColumn('id_card_front_path');
            }
            if (Schema::hasColumn('users', 'id_card_back_path')) {
                $table->dropColumn('id_card_back_path');
            }
            if (Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->dropColumn('terms_accepted_at');
            }
        });
    }
};
