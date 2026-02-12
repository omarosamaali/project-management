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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->enum('location_type', ['online', 'on_site'])->default('online');
            $table->string('online_link')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_map_url')->nullable();
            $table->text('venue_details')->nullable();
            $table->integer('counter')->default(0);
            $table->integer('count_days')->default(0);
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->longText('description_ar');
            $table->longText('description_en');

            // ← التعديل هنا
            $table->json('requirements')->nullable();       // أو ->default('[]')
            $table->json('features')->nullable();           // أو ->default('[]')
            $table->json('buttons')->nullable();            // أو ->default('[]')

            $table->string('main_image');
            $table->json('images')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('last_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
