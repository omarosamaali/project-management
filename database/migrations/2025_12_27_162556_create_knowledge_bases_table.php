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
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('kb_categories')->onDelete('cascade');
            $table->string('title');
            $table->longText('details');
            $table->string('attachments')->nullable(); // مسار الملفات
            $table->foreignId('added_by')->constrained('users'); // الشخص الذي أضاف
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
    }
};
