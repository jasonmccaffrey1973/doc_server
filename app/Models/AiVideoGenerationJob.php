<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiVideoGenerationJob extends Model
{
    protected $fillable = [
        'ai_video_scene_id', 'provider', 'status', 'provider_job_id', 'parameters',
        'error_message', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function scene(): BelongsTo
    {
        return $this->belongsTo(AiVideoScene::class, 'ai_video_scene_id');
    }
}
