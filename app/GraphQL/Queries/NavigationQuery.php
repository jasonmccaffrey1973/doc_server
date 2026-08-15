<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class NavigationQuery
{
    /**
     * @param  array<string, mixed>  $args
     * @return array<int, array<string, mixed>>
     */
    public function __invoke(null $_, array $args): array
    {
        $token = trim((string) ($args['token'] ?? ''));

        if (str_starts_with(strtolower($token), 'bearer ')) {
            $token = trim(substr($token, 7));
        }

        if ($token === '') {
            throw ValidationException::withMessages([
                'token' => [__('The token field is required.')],
            ]);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken || ! $accessToken->tokenable instanceof User) {
            throw ValidationException::withMessages([
                'token' => [__('The provided token is invalid.')],
            ]);
        }

        return $accessToken->tokenable->navigationLinks();
    }
}
