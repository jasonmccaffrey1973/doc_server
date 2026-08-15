<?php

namespace App\GraphQL\Mutations;

use App\Actions\Auth\AuthenticateUserByLogin;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginMutation
{
    /**
     * @param  array<string, mixed>  $args
     * @return array{token: string, user: User}
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args, [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $user = app(AuthenticateUserByLogin::class)->execute(
            (string) $args['login'],
            (string) $args['password'],
        );

        if (! $user) {
            throw ValidationException::withMessages([
                'login' => [__('These credentials do not match our records.')],
            ]);
        }

        $token = $user->createToken((string) ($args['device_name'] ?? 'graphql-client'))->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
