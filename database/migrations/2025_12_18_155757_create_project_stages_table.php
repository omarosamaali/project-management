<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('project_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('details')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stages');
    }
};
