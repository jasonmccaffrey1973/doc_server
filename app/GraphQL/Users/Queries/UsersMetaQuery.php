<?php

namespace App\GraphQL\Users\Queries;

use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class UsersMetaQuery
{
    /**
     * Resolve the `usersMeta` query. Returns total and filtered counts based on the same args as the users query.
     */
    public function __invoke($root, array $args, $context, ResolveInfo $resolveInfo): array
    {
        $totalCount = User::count();

        $query = User::query();

        // Apply search if provided
        if (!empty($args['search'] ?? null)) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Apply filters if provided (placeholder)
        if (!empty($args['filters'] ?? null) && is_array($args['filters'])) {
            foreach ($args['filters'] as $filter) {
                if (!empty($filter['field']) && array_key_exists('value', $filter)) {
                    $query->where($filter['field'], $filter['value']);
                }
            }
        }

        $filteredCount = $query->count();

        return [
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];
    }
}
