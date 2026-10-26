<?php

namespace App\Jobs;

use App\Models\AiVideoGenerationJob;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImportAiVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $generationJobId, public string $path) {}

    public function handle(MediaService $mediaService): void
    {
        $job = AiVideoGenerationJob::with('scene.project')->findOrFail($this->generationJobId);
        $disk = Storage::disk(config('ai_video.storage_disk'));
        $source = $disk->path($this->path);

        if (! is_file($source)) {
            throw new RuntimeException('The staged video file is missing.');
        }

        $job->update(['status' => 'processing', 'started_at' => now(), 'error_message' => null]);

        $metadata = $job->parameters ?? [];
        $upload = new UploadedFile(
            $source,
            $metadata['original_name'] ?? basename($source),
            $metadata['mime_type'] ?? 'video/mp4',
            UPLOAD_ERR_OK,
            true,
        );

        $media = ! empty($metadata['media_id'])
            ? Media::find($metadata['media_id'])
            : null;

        if (! $media) {
            $media = $mediaService->uploadMedia(
                file: $upload,
                mediaType: 'video',
                userId: $job->scene->project->user_id,
            );
            $metadata['media_id'] = $media->id;
            $job->update(['parameters' => $metadata]);
        }

        $job->scene->update(['output_asset_id' => $media->id, 'status' => 'review']);
        $job->update(['status' => 'succeeded', 'finished_at' => now()]);
        $disk->delete($this->path);
    }

    public function failed(?\Throwable $exception): void
    {
        AiVideoGenerationJob::whereKey($this->generationJobId)->update([
            'status' => 'failed',
            'error_message' => 'The video import failed. Please retry the upload.',
            'finished_at' => now(),
        ]);
    }
}
