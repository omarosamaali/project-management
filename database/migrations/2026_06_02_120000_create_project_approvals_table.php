<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('status')->default('pending'); // pending | approved
            $table->timestamps();
        });

        Schema::create('project_approval_approver', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_approval_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['project_approval_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_approval_approver');
        Schema::dropIfExists('project_approvals');
    }
};
