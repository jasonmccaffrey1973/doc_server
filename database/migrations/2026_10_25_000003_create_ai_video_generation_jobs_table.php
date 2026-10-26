<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_video_generation_jobs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_video_scene_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('manual');
            $table->string('status')->default('queued');
            $table->string('provider_job_id')->nullable();
            $table->json('parameters')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['provider', 'provider_job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_video_generation_jobs');
    }
};
