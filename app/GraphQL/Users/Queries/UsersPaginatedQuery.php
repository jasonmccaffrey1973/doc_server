<?php

namespace App\GraphQL\Users\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class UsersPaginatedQuery
{
    /**
     * Resolve the paginated users query. Supports search, filters, and sorting.
     */
    public function __invoke($root, array $args, $context, ResolveInfo $resolveInfo): Builder
    {
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

        // Apply filters if provided (placeholder, extend as needed)
        if (!empty($args['filters'] ?? null) && is_array($args['filters'])) {
            foreach ($args['filters'] as $filter) {
                // Example simple equality filter: field and value expected
                if (!empty($filter['field']) && array_key_exists('value', $filter)) {
                    $query->where($filter['field'], $filter['value']);
                }
            }
        }

        // Apply sorting if provided
        if (!empty($args['sort'] ?? null) && !empty($args['sort']['field'])) {
            $fieldMap = [
                'NAME' => 'name',
                'EMAIL' => 'email',
                'USERNAME' => 'username',
                'CREATED_AT' => 'created_at',
            ];
            $column = $fieldMap[$args['sort']['field']] ?? null;
            $direction = $args['sort']['direction'] ?? 'ASC';
            if ($column) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }
}
