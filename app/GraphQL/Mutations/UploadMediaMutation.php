<?php

namespace App\GraphQL\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UploadMediaMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{input: array{file: \Illuminate\Http\UploadedFile, mediaType: string, storageLocationId?: string}} $args
     */
    public function __invoke(null $_, array $args): Media
    {
        Validator::make($args['input'], [
            'file' => ['required', 'file', 'max:512000'], // 500MB max
            'mediaType' => ['required', 'in:image,video,audio'],
            'storageLocationId' => ['nullable', 'exists:storage_locations,id'],
        ])->validate();

        return $this->mediaService->uploadMedia(
            file: $args['input']['file'],
            mediaType: $args['input']['mediaType'],
            storageLocationId: $args['input']['storageLocationId'] ?? null,
            userId: Auth::id()
        );
    }
}
