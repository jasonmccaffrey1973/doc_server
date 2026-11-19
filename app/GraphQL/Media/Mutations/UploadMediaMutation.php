<?php

namespace App\GraphQL\Media\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UploadMediaMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{kind: string, file: \Illuminate\Http\UploadedFile, altText?: string, storageLocation?: string} $args
     */
    public function __invoke(null $_, array $args): Media
    {
        Validator::make($args, [
            'file' => ['required', 'file', 'max:512000'], // 500MB max
            'kind' => ['required', 'in:image,video,audio'],
            'storageLocation' => ['nullable', 'exists:storage_locations,id'],
            'altText' => ['nullable', 'string', 'max:500'],
        ])->validate();

        return $this->mediaService->uploadMedia(
            file: $args['file'],
            mediaType: $args['kind'],
            storageLocationId: $args['storageLocation'] ?? null,
            userId: Auth::id(),
            altText: $args['altText'] ?? null
        );
    }
}
