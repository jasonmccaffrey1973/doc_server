<?php

namespace App\GraphQL\Mutations;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RegisterMutation
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    /**
     * @param  array<string, mixed>  $args
     * @return array{token: string, user: User}
     */
    public function __invoke(null $_, array $args): array
    {
        Validator::make($args, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'device_name' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $user = User::create([
            'name' => (string) $args['name'],
            'username' => (string) $args['username'],
            'email' => (string) $args['email'],
            'password' => (string) $args['password'],
        ]);

        $token = $user->createToken((string) ($args['device_name'] ?? 'graphql-client'))->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
