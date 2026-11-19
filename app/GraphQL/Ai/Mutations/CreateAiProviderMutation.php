<?php

namespace App\GraphQL\Ai\Mutations;

use App\GraphQL\Ai\AiVideoAccess;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAiProviderMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiProvider
    {
        $this->currentUserId();

        $input = $args['input'] ?? [];

        $data = Validator::make($input, [
            'key' => ['required', 'string', 'max:64', 'unique:ai_providers,key'],
            'name' => ['required', 'string', 'max:120'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', 'string', 'max:64'],
            'defaultBaseUrl' => ['nullable', 'string', 'max:2048'],
            'authType' => ['required', 'string', 'max:64'],
            'credentialFields' => ['required', 'array'],
            'credentialFields.*.key' => ['required', 'string', 'max:64'],
            'credentialFields.*.label' => ['required', 'string', 'max:120'],
            'credentialFields.*.secret' => ['required', 'boolean'],
            'credentialHeader' => ['nullable', 'string', 'max:255'],
            'apiKeyUrl' => ['nullable', 'string', 'max:2048'],
        ])->validate();

        return AiProvider::create([
            'key' => Str::slug($data['key'], '_'),
            'name' => $data['name'],
            'services' => $data['services'],
            'default_base_url' => $data['defaultBaseUrl'] ?? '',
            'auth_type' => $data['authType'],
            'credential_fields' => $data['credentialFields'],
            'credential_header' => $data['credentialHeader'] ?? null,
            'api_key_url' => $data['apiKeyUrl'] ?? null,
            'capabilities' => $data['services'],
            'is_enabled' => true,
        ]);
    }
}
