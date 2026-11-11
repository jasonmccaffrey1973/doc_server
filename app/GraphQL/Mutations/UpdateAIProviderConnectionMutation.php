<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProvider;
use App\Models\AiProviderConnection;
use Illuminate\Support\Facades\Validator;

class UpdateAIProviderConnectionMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): AiProviderConnection
    {
        $data = Validator::make($args['input'], [
            'providerId' => ['sometimes', 'required', 'uuid', 'exists:ai_providers,id'],
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'service' => ['sometimes', 'required', 'in:text,image,video,voice,music'],
            'baseUrl' => ['sometimes', 'required', 'string', 'max:2048'],
            'authType' => ['sometimes', 'required', 'in:api-key,bearer,basic,access-secret,none'],
            'credentials' => ['sometimes', 'required', 'array'],
            'credentials.*.key' => ['required', 'string', 'max:120'],
            'credentials.*.value' => ['required', 'string', 'max:8192'],
            'modelIds' => ['sometimes', 'required', 'array'],
            'modelIds.*' => ['required', 'string', 'max:255'],
        ])->validate();

        $connection = AiProviderConnection::query()
            ->where('user_id', $this->currentUserId())
            ->findOrFail($args['id']);

        $updates = [];
        foreach (['name' => 'label', 'service' => 'service', 'baseUrl' => 'base_url', 'authType' => 'auth_type', 'modelIds' => 'model_ids'] as $inputKey => $column) {
            if (array_key_exists($inputKey, $data)) {
                $updates[$column] = $data[$inputKey];
            }
        }
        if (array_key_exists('providerId', $data)) {
            $provider = AiProvider::query()->whereKey($data['providerId'])->where('is_enabled', true)->firstOrFail();
            $updates['ai_provider_id'] = $provider->id;
        }
        if (array_key_exists('credentials', $data)) {
            $updates['credentials'] = collect($data['credentials'])
                ->mapWithKeys(fn (array $credential) => [$credential['key'] => $credential['value']])
                ->all();
        }

        $connection->fill($updates)->save();

        return $connection->load('provider');
    }
}
