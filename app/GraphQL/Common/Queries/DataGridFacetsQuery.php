<?php

namespace App\GraphQL\Common\Queries;

use App\GraphQL\Users\Queries\UsersPaginatedQuery;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Nuwave\Lighthouse\Execution\ResolveInfo;

class DataGridFacetsQuery
{
    private const MAX_LIMIT = 200;

    /**
     * Resolve distinct facet options and counts for each requested filterable field.
     *
     * @param  array<string, mixed>  $args
     * @return array<int, array{
     *     field: string,
     *     options: array<int, array{value: string, label: string, count: int}>,
     *     totalCount: int,
     *     hasMore: bool,
     * }>
     */
    public function __invoke(mixed $root, array $args, mixed $context, ResolveInfo $resolveInfo): array
    {
        $resource = (string) ($args['resource'] ?? 'users');
        if ($resource !== 'users') {
            throw ValidationException::withMessages([
                'resource' => ["Unsupported datagrid resource: {$resource}."],
            ]);
        }

        $fields = $args['fields'] ?? [];
        if (! is_array($fields)) {
            throw ValidationException::withMessages([
                'fields' => ['The fields argument must be an array.'],
            ]);
        }

        foreach ($fields as $field) {
            if (! is_string($field) || ! array_key_exists($field, UsersPaginatedQuery::FILTERABLE_FIELDS)) {
                throw ValidationException::withMessages([
                    'fields' => ["Invalid facet field: {$field}."],
                ]);
            }
        }

        $limit = max(1, min((int) ($args['limit'] ?? 50), self::MAX_LIMIT));
        $offset = max(0, (int) ($args['offset'] ?? 0));
        $facetSearch = is_string($args['facetSearch'] ?? null) ? trim($args['facetSearch']) : '';
        $filters = is_array($args['filters'] ?? null) ? $args['filters'] : [];
        $search = is_string($args['search'] ?? null) ? $args['search'] : null;

        $facets = [];

        foreach ($fields as $field) {
            $column = UsersPaginatedQuery::FILTERABLE_FIELDS[$field];

            // Exclude this facet's own filter so its options stay selectable.
            $otherFilters = array_values(array_filter(
                $filters,
                fn (mixed $filter): bool => is_array($filter) && ($filter['field'] ?? null) !== $field
            ));

            $query = UsersPaginatedQuery::applySearchAndFilters(User::query(), [
                'search' => $search,
                'filters' => $otherFilters,
            ]);

            $query->whereNotNull($column)->where($column, '!=', '');

            if ($facetSearch !== '') {
                $query->whereLike($column, "%{$facetSearch}%", caseSensitive: false);
            }

            $totalCount = (clone $query)->distinct()->count($column);

            $rows = (clone $query)
                ->select($column)
                ->selectRaw('COUNT(*) as aggregate_count')
                ->groupBy($column)
                ->orderByDesc('aggregate_count')
                ->orderBy($column)
                ->offset($offset)
                ->limit($limit)
                ->get();

            $options = $rows->map(static function (User $row) use ($column): array {
                $value = (string) $row->getAttribute($column);

                return [
                    'value' => $value,
                    'label' => $value,
                    'count' => (int) $row->getAttribute('aggregate_count'),
                ];
            })->all();

            $facets[] = [
                'field' => $field,
                'options' => $options,
                'totalCount' => $totalCount,
                'hasMore' => ($offset + count($options)) < $totalCount,
            ];
        }

        return $facets;
    }
}
