<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'key' => 'openai',
                'name' => 'OpenAI',
                'services' => ['text', 'image', 'voice'],
                'default_base_url' => 'https://api.openai.com/v1',
                'auth_type' => 'api-key',
                'credential_fields' => [
                    ['key' => 'apiKey', 'label' => 'API key', 'secret' => true],
                ],
                'credential_header' => 'Authorization: Bearer',
                'api_key_url' => 'https://platform.openai.com/api-keys',
                'capabilities' => ['script_generation', 'text', 'image', 'voice'],
                'is_enabled' => true,
            ],
            [
                'key' => 'runway',
                'name' => 'Runway',
                'services' => ['image', 'video'],
                'default_base_url' => 'https://api.dev.runwayml.com',
                'auth_type' => 'api-key',
                'credential_fields' => [
                    ['key' => 'apiKey', 'label' => 'API key', 'secret' => true],
                ],
                'credential_header' => 'Authorization: Bearer',
                'api_key_url' => 'https://dev.runwayml.com',
                'capabilities' => ['image', 'video'],
                'is_enabled' => true,
            ],
            [
                'key' => 'kling',
                'name' => 'Kling',
                'services' => ['image', 'video'],
                'default_base_url' => 'https://api.klingai.com',
                'auth_type' => 'access-secret',
                'credential_fields' => [
                    ['key' => 'accessKey', 'label' => 'Access key', 'secret' => true],
                    ['key' => 'secretKey', 'label' => 'Secret key', 'secret' => true],
                ],
                'credential_header' => null,
                'api_key_url' => 'https://klingai.com/dev/api-key',
                'capabilities' => ['image', 'video'],
                'is_enabled' => true,
            ],
            [
                'key' => 'elevenlabs',
                'name' => 'ElevenLabs',
                'services' => ['voice', 'music'],
                'default_base_url' => 'https://api.elevenlabs.io/v1',
                'auth_type' => 'api-key',
                'credential_fields' => [
                    ['key' => 'apiKey', 'label' => 'API key', 'secret' => true],
                ],
                'credential_header' => 'xi-api-key',
                'api_key_url' => 'https://elevenlabs.io/app/settings/api-keys',
                'capabilities' => ['voice', 'music'],
                'is_enabled' => true,
            ],
            [
                'key' => 'custom',
                'name' => 'Custom provider',
                'services' => ['text', 'image', 'video', 'voice', 'music'],
                'default_base_url' => '',
                'auth_type' => 'bearer',
                'credential_fields' => [
                    ['key' => 'token', 'label' => 'Bearer token', 'secret' => true],
                ],
                'credential_header' => null,
                'api_key_url' => null,
                'capabilities' => ['text', 'image', 'video', 'voice', 'music'],
                'is_enabled' => true,
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::updateOrCreate(
                ['key' => $provider['key']],
                $provider,
            );
        }
    }
}
