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
        Schema::create('chapter_lessons', function (Blueprint $table) {
            $table->foreignUuid('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->foreignUuid('course_lesson_id')->constrained('course_lessons')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->primary(['chapter_id', 'course_lesson_id']);
            $table->unique(['chapter_id', 'position']);
            $table->index('course_lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_lessons');
    }
};
