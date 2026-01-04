<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->text('comment');
            $table->string('image')->nullable();
            $table->boolean('is_solution')->default(false);
            $table->timestamps();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreignId('solution_comment_id')->nullable()->constrained('issue_comments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['solution_comment_id']);
            $table->dropColumn('solution_comment_id');
        });

        Schema::dropIfExists('issue_comments');
    }
};
