<?php

namespace App\GraphQL\Users\Queries;

use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class UsersQuery
{
    /**
     * Resolve the `users` query.
     */
    public function __invoke($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        return User::all();
    }
}
