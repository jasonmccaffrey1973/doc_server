<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GenerateAiVideoScriptMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): never
    {
        $this->ownedScene($args['sceneId']);
        Validator::make($args, ['brief' => ['required', 'string', 'max:10000']])->validate();

        if (config('ai_video.script_provider', 'manual') === 'manual') {
            throw ValidationException::withMessages([
                'script_provider' => 'AI script generation is disabled. Configure a server-side provider to enable it.',
            ]);
        }

        throw ValidationException::withMessages([
            'script_provider' => 'The configured AI script provider is not available yet.',
        ]);
    }
}
