<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_video_scenes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_video_project_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->unsignedInteger('position')->default(0);
            $table->longText('script')->nullable();
            $table->text('visual_prompt')->nullable();
            // Media uses UUID keys in this application; keep these optional references loosely coupled.
            $table->uuid('character_asset_id')->nullable();
            $table->uuid('location_asset_id')->nullable();
            $table->uuid('output_asset_id')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->default(10);
            $table->string('aspect_ratio', 20)->default('16:9');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->index(['ai_video_project_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_video_scenes');
    }
};
