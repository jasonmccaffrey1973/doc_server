<?php

namespace App\GraphQL\Users\Queries;

use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class UsersMetaQuery
{
    /**
     * Resolve the `usersMeta` query. Returns total and filtered counts based on the same args as the users query.
     *
     * @param  array<string, mixed>  $args
     * @return array{totalCount: int, filteredCount: int}
     */
    public function __invoke(mixed $root, array $args, mixed $context, ResolveInfo $resolveInfo): array
    {
        $totalCount = User::count();
        $filteredCount = UsersPaginatedQuery::applySearchAndFilters(User::query(), $args)->count();

        return [
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];
    }
}
