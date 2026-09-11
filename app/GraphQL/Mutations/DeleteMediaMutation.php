<?php

namespace App\GraphQL\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Validator;

class DeleteMediaMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{id: string} $args
     */
    public function __invoke(null $_, array $args): bool
    {
        Validator::make($args, [
            'id' => ['required', 'exists:media,id'],
        ])->validate();

        $media = Media::findOrFail($args['id']);
        return $this->mediaService->deleteMedia($media);
    }
}
