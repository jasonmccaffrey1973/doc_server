<?php

return [
    'storage_disk' => env('AI_VIDEO_STORAGE_DISK', 'ai_video'),
    'max_upload_mb' => (int) env('AI_VIDEO_MAX_UPLOAD_MB', 500),
    'generation_provider' => env('AI_VIDEO_GENERATION_PROVIDER', 'manual'),
    'script_provider' => env('AI_SCRIPT_PROVIDER', 'manual'),
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL'),
    ],
];
