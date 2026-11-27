<?php

namespace App\GraphQL\Users\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class UsersPaginatedQuery
{
    /**
     * Allowed filterable and facetable fields mapped to database columns.
     *
     * @var array<string, string>
     */
    public const FILTERABLE_FIELDS = [
        'name' => 'name',
        'username' => 'username',
        'email' => 'email',
    ];

    /**
     * Resolve the paginated users query. Supports search, filters, and sorting.
     *
     * @param  array<string, mixed>  $args
     */
    public function __invoke(mixed $root, array $args, mixed $context, ResolveInfo $resolveInfo): Builder
    {
        $query = self::applySearchAndFilters(User::query(), $args);

        // Apply sorting if provided
        if (! empty($args['sort']) && is_array($args['sort']) && ! empty($args['sort']['field'])) {
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

    /**
     * Apply search and validated filter criteria to a User query builder.
     *
     * @param  Builder<User>  $query
     * @param  array<string, mixed>  $args
     * @return Builder<User>
     */
    public static function applySearchAndFilters(Builder $query, array $args): Builder
    {
        if (! empty($args['search']) && is_string($args['search'])) {
            $search = "%{$args['search']}%";
            $query->where(function (Builder $q) use ($search) {
                $q->whereLike('name', $search, caseSensitive: false)
                    ->orWhereLike('email', $search, caseSensitive: false)
                    ->orWhereLike('username', $search, caseSensitive: false);
            });
        }

        if (! empty($args['filters']) && is_array($args['filters'])) {
            $groupedFilters = [];

            foreach ($args['filters'] as $filter) {
                if (! is_array($filter) || empty($filter['field']) || ! array_key_exists('value', $filter)) {
                    continue;
                }

                $field = (string) $filter['field'];
                $column = self::FILTERABLE_FIELDS[$field] ?? null;

                if ($column === null) {
                    throw ValidationException::withMessages([
                        'filters' => ["Invalid filter field: {$field}."],
                    ]);
                }

                $groupedFilters[$column][] = $filter['value'];
            }

            foreach ($groupedFilters as $column => $values) {
                $query->whereIn($column, $values);
            }
        }

        return $query;
    }
}
