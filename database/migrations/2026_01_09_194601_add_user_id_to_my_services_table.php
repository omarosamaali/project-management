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
        Schema::table('my_services', function (Blueprint $table) {
            if (!Schema::hasColumn('my_services', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            } else {
                // إذا كان العمود موجوداً ولكن ينقصه الربط (Foreign Key)
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            }        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_services', function (Blueprint $table) {
            //
        });
    }
};
