<?php

namespace App\GraphQL\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UploadMediaFromUrlMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{kind: string, url: string, altText?: string, storageLocation?: string} $args
     */
    public function __invoke(null $_, array $args): Media
    {
        Validator::make($args, [
            'url' => ['required', 'url', 'max:2000'],
            'kind' => ['required', 'in:image,video,audio'],
            'storageLocation' => ['nullable', 'exists:storage_locations,id'],
            'altText' => ['nullable', 'string', 'max:500'],
        ])->validate();

        return $this->mediaService->uploadMediaFromUrl(
            url: $args['url'],
            mediaType: $args['kind'],
            storageLocationId: $args['storageLocation'] ?? null,
            userId: Auth::id(),
            altText: $args['altText'] ?? null
        );
    }
}
