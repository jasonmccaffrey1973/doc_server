<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateAiVideoSceneMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        $scene = $this->ownedScene($args['id']);
        $data = Validator::make($args['input'], [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'script' => ['sometimes', 'nullable', 'string'],
            'visual_prompt' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'character_asset_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('media', 'id')->where('user_id', $this->currentUserId())],
            'location_asset_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('media', 'id')->where('user_id', $this->currentUserId())],
            'duration_seconds' => ['sometimes', 'integer', 'min:1', 'max:65535'],
            'aspect_ratio' => ['sometimes', 'string', 'max:20'],
            'status' => ['sometimes', 'in:draft,queued,generating,review,approved,failed'],
        ])->validate();

        $scene->update($data);

        return $scene->refresh();
    }
}
