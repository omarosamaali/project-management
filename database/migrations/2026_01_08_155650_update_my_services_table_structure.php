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
            // 1. Safely drop foreign keys only if they exist
            // We use an array of names or raw SQL to ensure it doesn't crash
            if (Schema::hasColumn('my_services', 'service_id')) {
                // Check if the index/foreign key actually exists in the DB
                $table->dropForeign(['service_id']);
            }
            if (Schema::hasColumn('my_services', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            /** 2. Drop columns only if they exist */
            $columnsToDrop = [
                'title',
                'service_id',
                'user_id',
                'price',
                'duration',
                'description',
                'what_you_will_get'
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('my_services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('my_services', function (Blueprint $table) {
            /** 3. Add new columns (Skipping 'id' as it exists) */
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('counter')->default(0);
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->integer('execution_days_from');
            $table->integer('execution_days_to');
            $table->integer('support_days')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en');
            $table->json('requirements');
            $table->json('features');
            $table->string('main_image');
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }
    /**
     * Reverse the migrations.
     */
};
