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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('counter')->default(0);
            
            $table->boolean('system_external')->default(false);
            $table->string('external_url')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->integer('execution_days_from');
            $table->integer('execution_days_to');
            $table->integer('support_days')->nullable();
            $table->longText('description_ar');
            $table->longText('description_en');
            $table->json('requirements');
            $table->json('features');
            $table->json('buttons')->nullable();
            $table->string('main_image');
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
