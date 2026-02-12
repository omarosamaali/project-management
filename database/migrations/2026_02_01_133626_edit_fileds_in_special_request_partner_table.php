<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_request_partner', function (Blueprint $table) {
            $table->enum('share_type', ['percentage', 'fixed'])->default('percentage')->after('profit_share_percentage');
            $table->decimal('fixed_amount', 10, 2)->nullable()->after('share_type');
        });
    }

    public function down(): void
    {
        Schema::table('special_request_partner', function (Blueprint $table) {
            $table->dropColumn(['share_type', 'fixed_amount']);
        });
    }
};
