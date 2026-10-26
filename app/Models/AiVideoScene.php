<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiVideoScene extends Model
{
    protected $fillable = [
        'ai_video_project_id', 'title', 'position', 'script', 'visual_prompt',
        'character_asset_id', 'location_asset_id', 'output_asset_id',
        'duration_seconds', 'aspect_ratio', 'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(AiVideoProject::class, 'ai_video_project_id');
    }

    public function generationJobs(): HasMany
    {
        return $this->hasMany(AiVideoGenerationJob::class, 'ai_video_scene_id')->latest();
    }
}
