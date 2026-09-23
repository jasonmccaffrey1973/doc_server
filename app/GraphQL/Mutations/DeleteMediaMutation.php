<?php

namespace App\GraphQL\Mutations;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Support\Facades\Validator;

class DeleteMediaMutation
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * @param array{ids: array<string>} $args
     * @return array{success: bool, deletedCount: int}
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args, [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string', 'exists:media,id'],
        ])->validate();

        $deletedCount = 0;
        foreach ($args['ids'] as $id) {
            $media = Media::find($id);
            if ($media) {
                $this->mediaService->deleteMedia($media);
                $deletedCount++;
            }
        }

        return [
            'success' => $deletedCount > 0,
            'deletedCount' => $deletedCount,
        ];
    }
}
