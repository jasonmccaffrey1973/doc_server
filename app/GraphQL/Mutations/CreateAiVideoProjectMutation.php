<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiVideoProject;
use Illuminate\Support\Facades\Validator;

class CreateAiVideoProjectMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiVideoProject
    {
        $data = Validator::make($args['input'], [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
        ])->validate();

        return AiVideoProject::create([...$data, 'user_id' => $this->currentUserId()]);
    }
}
