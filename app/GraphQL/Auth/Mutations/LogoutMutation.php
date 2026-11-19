<?php

namespace App\GraphQL\Auth\Mutations;

class LogoutMutation
{
    public function __invoke(): bool
    {
        $user = auth('sanctum')->user();

        if (! $user) {
            return false;
        }

        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();

            return true;
        }

        $user->tokens()->delete();

        return true;
    }
}
