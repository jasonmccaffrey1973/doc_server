<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Jobs\ImportAiVideo;
use App\Models\AiVideoGenerationJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class ImportAiVideoMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiVideoGenerationJob
    {
        $scene = $this->ownedScene($args['sceneId']);
        $maxKb = max(1, (int) config('ai_video.max_upload_mb', 500)) * 1024;
        $data = Validator::make($args, [
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', "max:{$maxKb}"],
        ])->validate();
        $file = $data['video'];

        $job = AiVideoGenerationJob::create([
            'ai_video_scene_id' => $scene->id,
            'provider' => 'manual',
            'status' => 'queued',
            'parameters' => [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ],
        ]);

        $directory = "projects/{$scene->ai_video_project_id}/scenes/{$scene->id}/imports";
        $filename = Str::uuid().'.'.($file->extension() ?: 'mp4');

        try {
            $path = Storage::disk(config('ai_video.storage_disk'))->putFileAs($directory, $file, $filename);

            if (! $path) {
                throw new RuntimeException('Unable to store the uploaded video.');
            }

            ImportAiVideo::dispatch($job->id, $path)->afterCommit();
        } catch (\Throwable $exception) {
            $job->update([
                'status' => 'failed',
                'error_message' => 'The video could not be queued for import.',
                'finished_at' => now(),
            ]);
            throw new RuntimeException('The video could not be queued for import.', previous: $exception);
        }

        return $job;
    }
}
