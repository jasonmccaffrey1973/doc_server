<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignUuid('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('title');
            $table->jsonb('content')->default(DB::raw("'{\"type\":\"doc\",\"content\":[]}'::jsonb"));
            $table->unsignedInteger('source_version');
            $table->timestamps();

            $table->unique(['course_id', 'lesson_id']);
            $table->index('course_id');
            $table->index('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
