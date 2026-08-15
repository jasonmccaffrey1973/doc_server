<?php

namespace App\GraphQL\Queries;

use App\Models\User;

class MeQuery
{
    public function __invoke(): ?User
    {
        /** @var User|null $user */
        $user = auth('sanctum')->user();

        return $user;
    }
}
