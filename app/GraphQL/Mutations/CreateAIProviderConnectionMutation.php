<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProvider;
use App\Models\AiProviderConnection;
use Illuminate\Support\Facades\Validator;

class CreateAIProviderConnectionMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiProviderConnection
    {
        $input = $args['input'];
        $data = Validator::make($input, [
            'providerId' => ['required', 'uuid', 'exists:ai_providers,id'],
            'name' => ['required', 'string', 'max:120'],
            'service' => ['required', 'in:text,image,video,voice,music'],
            'baseUrl' => ['required', 'string', 'max:2048'],
            'authType' => ['required', 'in:api-key,bearer,basic,access-secret,none'],
            'credentials' => ['required', 'array'],
            'credentials.*.key' => ['required', 'string', 'max:120'],
            'credentials.*.value' => ['required', 'string', 'max:8192'],
            'modelIds' => ['required', 'array'],
            'modelIds.*' => ['required', 'string', 'max:255'],
        ])->validate();

        $provider = AiProvider::query()->whereKey($data['providerId'])->where('is_enabled', true)->firstOrFail();
        $credentials = collect($data['credentials'])->mapWithKeys(fn (array $credential) => [$credential['key'] => $credential['value']])->all();

        $connection = AiProviderConnection::query()->create([
            'user_id' => $this->currentUserId(),
            'ai_provider_id' => $provider->id,
            'label' => $data['name'],
            'service' => $data['service'],
            'base_url' => $data['baseUrl'],
            'auth_type' => $data['authType'],
            'credentials' => $credentials,
            'model_ids' => $data['modelIds'],
            'status' => 'not-tested',
            'is_active' => true,
        ]);

        return $connection->load('provider');
    }
}
