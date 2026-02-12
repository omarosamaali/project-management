<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('country');
            }

            if (Schema::hasColumn('users', 'skills')) {
                $table->text('skills')->nullable()->change();
            } else {
                $table->text('skills')->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'id_card_path')) {
                $table->string('id_card_path')->nullable();
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'verification_video')) {
                $table->string('verification_video')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country', 'phone', 'skills', 'id_card_path', 'verification_video']);
        });
    }
};