<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\AiVideoAccess;
use App\Models\AiProvider;
use App\Models\AiProviderConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\ConnectionException;

class TestAIProviderConnectionMutation
{
    use AiVideoAccess;

    public function __invoke(null $_, array $args): array
    {
        $data = Validator::make($args['input'], [
            'providerId' => ['required', 'uuid', 'exists:ai_providers,id'],
            'name' => ['required', 'string', 'max:120'],
            'service' => ['required', 'in:text,image,video,voice,music'],
            'baseUrl' => ['required', 'url', 'max:2048'],
            'authType' => ['required', 'in:api-key,bearer,basic,access-secret,none'],
            'credentials' => ['required', 'array'],
            'credentials.*.key' => ['required', 'string', 'max:120'],
            'credentials.*.value' => ['required', 'string', 'max:8192'],
            'modelIds' => ['required', 'array'],
            'modelIds.*' => ['required', 'string', 'max:255'],
        ])->validate();

        $userId = $this->currentUserId();
        $provider = AiProvider::query()->whereKey($data['providerId'])->where('is_enabled', true)->firstOrFail();

        $url = rtrim($data['baseUrl'], '/');
        $parts = parse_url($url);
        if (! $parts || strtolower($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            return $this->result(false, 'Use a public HTTPS base URL without embedded credentials.');
        }
        if (! $this->hostResolvesPublicly($parts['host'])) {
            return $this->result(false, 'The base URL must resolve to a public host.');
        }

        $credentials = collect($data['credentials'])->mapWithKeys(
            fn (array $credential) => [$credential['key'] => $credential['value']],
        )->all();
        if ($credentials === [] && ! empty($args['id'])) {
            $savedConnection = AiProviderConnection::query()
                ->where('user_id', $userId)
                ->where('ai_provider_id', $data['providerId'])
                ->find($args['id']);
            if (! $savedConnection) {
                return $this->result(false, 'No saved credentials are available for this connection.');
            }
            $credentials = $savedConnection->credentials ?? [];
        }
        $request = Http::acceptJson()->timeout(10)->withoutRedirecting();

        switch ($data['authType']) {
            case 'api-key':
                $key = $credentials['apiKey'] ?? $credentials['api_key'] ?? $credentials['key'] ?? reset($credentials);
                if (! is_string($key) || $key === '') {
                    $message = $provider->key === 'openai'
                        ? 'An OpenAI API key is not configured. Add a key before testing this connection.'
                        : 'Provide an API key to test this connection.';
                    return $this->result(false, $message);
                }
                $request = $provider->key === 'openai'
                    ? $request->withToken($key)
                    : $request->withHeaders(['X-API-Key' => $key]);
                break;
            case 'bearer':
                $key = $credentials['token'] ?? $credentials['apiKey'] ?? $credentials['api_key'] ?? $credentials['key'] ?? reset($credentials);
                if (! is_string($key) || $key === '') {
                    return $this->result(false, 'Provide a bearer token to test this connection.');
                }
                $request = $request->withToken($key);
                break;
            case 'basic':
                $username = $credentials['username'] ?? $credentials['user'] ?? null;
                $password = $credentials['password'] ?? null;
                if (! is_string($username) || ! is_string($password)) {
                    return $this->result(false, 'Provide username and password credentials to test this connection.');
                }
                $request = $request->withBasicAuth($username, $password);
                break;
            case 'access-secret':
                $access = $credentials['accessKey'] ?? $credentials['access_key'] ?? $credentials['access'] ?? null;
                $secret = $credentials['secretKey'] ?? $credentials['secret_key'] ?? $credentials['secret'] ?? null;
                if (! is_string($access) || ! is_string($secret)) {
                    return $this->result(false, 'Provide accessKey and secretKey credentials to test this connection.');
                }
                $request = $request->withHeaders(['X-Access-Key' => $access, 'X-Secret-Key' => $secret]);
                break;
        }

        $testUrl = str_ends_with($url, '/models') ? $url : $url.'/models';
        try {
            $response = $request->get($testUrl);
            if ($response->successful()) {
                return $this->result(true, 'Connection test succeeded.');
            }
            if (in_array($response->status(), [401, 403], true)) {
                return $this->result(false, 'The provider rejected the credentials.');
            }

            return $this->result(false, 'The provider returned HTTP '.$response->status().' for its models endpoint.');
        } catch (ConnectionException $exception) {
            Log::warning('AI provider connection test could not connect.', [
                'provider_id' => $provider->id,
                'host' => $parts['host'],
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);
            if (str_contains($exception->getMessage(), 'cURL error 60')) {
                return $this->result(false, 'The backend could not verify the provider TLS certificate. Configure PHP cURL with a trusted CA bundle and retry.');
            }

            return $this->result(false, 'Could not reach the provider. Check the URL and try again.');
        }
    }

    private function hostResolvesPublicly(string $host): bool
    {
        if (str_ends_with(strtolower($host), '.localhost') || strtolower($host) === 'localhost' || str_ends_with(strtolower($host), '.local')) {
            return false;
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : (gethostbynamel($host) ?: []);

        if (! filter_var($host, FILTER_VALIDATE_IP) && function_exists('dns_get_record')) {
            foreach (dns_get_record($host, DNS_AAAA) ?: [] as $record) {
                if (! empty($record['ipv6'])) {
                    $addresses[] = $record['ipv6'];
                }
            }
        }

        if ($addresses === []) {
            return false;
        }

        foreach (array_unique($addresses) as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    private function result(bool $success, string $message): array
    {
        return compact('success', 'message');
    }
}
