<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use Illuminate\Support\Facades\Validator;

class UpdateAiVideoProjectMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args)
    {
        $project = $this->ownedProject($args['id']);
        $data = Validator::make($args['input'], [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'status' => ['sometimes', 'in:draft,in_production,review,approved,archived'],
        ])->validate();

        $project->update($data);

        return $project->refresh();
    }
}
