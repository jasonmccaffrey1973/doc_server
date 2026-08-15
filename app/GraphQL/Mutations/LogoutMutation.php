<?php

namespace App\GraphQL\Mutations;

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
