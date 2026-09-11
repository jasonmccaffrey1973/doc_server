<?php

namespace App\GraphQL\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BulkUploadMediaMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{input: array{files: array, mediaType: string, storageLocationId?: string}} $args
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args['input'], [
            'files' => ['required', 'array', 'min:1', 'max:50'],
            'files.*' => ['required', 'file', 'max:512000'], // 500MB max per file
            'mediaType' => ['required', 'in:image,video,audio'],
            'storageLocationId' => ['nullable', 'exists:storage_locations,id'],
        ])->validate();

        return $this->mediaService->bulkUploadMedia(
            files: $args['input']['files'],
            mediaType: $args['input']['mediaType'],
            storageLocationId: $args['input']['storageLocationId'] ?? null,
            userId: Auth::id()
        );
    }
}
