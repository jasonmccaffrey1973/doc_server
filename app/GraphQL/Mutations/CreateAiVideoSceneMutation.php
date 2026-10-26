<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiVideoScene;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateAiVideoSceneMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiVideoScene
    {
        $project = $this->ownedProject($args['projectId']);
        $data = Validator::make($args['input'], [
            'title' => ['required', 'string', 'max:200'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'script' => ['nullable', 'string'],
            'visual_prompt' => ['nullable', 'string', 'max:10000'],
            'character_asset_id' => ['nullable', 'uuid', Rule::exists('media', 'id')->where('user_id', $this->currentUserId())],
            'location_asset_id' => ['nullable', 'uuid', Rule::exists('media', 'id')->where('user_id', $this->currentUserId())],
            'duration_seconds' => ['sometimes', 'integer', 'min:1', 'max:65535'],
            'aspect_ratio' => ['sometimes', 'string', 'max:20'],
        ])->validate();

        return $project->scenes()->create($data);
    }
}
