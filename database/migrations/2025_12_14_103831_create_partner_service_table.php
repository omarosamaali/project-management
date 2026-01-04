<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('partner_service', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('partner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('service_id')->references('id')->on('services')->onDelete('cascade');

            $table->unique(['partner_id', 'service_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_service');
    }
};