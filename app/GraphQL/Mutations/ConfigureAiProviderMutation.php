<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProvider;
use App\Models\AiProviderConnection;
use Illuminate\Support\Facades\Validator;

class ConfigureAiProviderMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiProviderConnection
    {
        $userId = $this->currentUserId();
        $data = Validator::make($args, [
            'providerKey' => ['required', 'string', 'exists:ai_providers,key'],
            'label' => ['nullable', 'string', 'max:120'],
            'credentials' => ['required', 'array', 'min:1'],
            'credentials.*' => ['required', 'string', 'max:8192'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        $provider = AiProvider::query()
            ->where('key', $data['providerKey'])
            ->where('is_enabled', true)
            ->firstOrFail();

        $connection = AiProviderConnection::updateOrCreate(
            ['user_id' => $userId, 'ai_provider_id' => $provider->id],
            [
                'label' => $data['label'] ?? $provider->name,
                'credentials' => $data['credentials'],
                'settings' => $data['settings'] ?? null,
                'is_active' => true,
            ],
        );

        return $connection->load('provider');
    }
}
