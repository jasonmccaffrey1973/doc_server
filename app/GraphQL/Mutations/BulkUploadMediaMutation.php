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
     * @param array{kind: string, files: array, altText?: string, storageLocation?: string} $args
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args, [
            'files' => ['required', 'array', 'min:1', 'max:50'],
            'files.*' => ['required', 'file', 'max:512000'], // 500MB max per file
            'kind' => ['required', 'in:image,video,audio'],
            'storageLocation' => ['nullable', 'exists:storage_locations,id'],
            'altText' => ['nullable', 'string', 'max:500'],
        ])->validate();

        return $this->mediaService->bulkUploadMedia(
            files: $args['files'],
            mediaType: $args['kind'],
            storageLocationId: $args['storageLocation'] ?? null,
            userId: Auth::id(),
            altText: $args['altText'] ?? null
        );
    }
}
