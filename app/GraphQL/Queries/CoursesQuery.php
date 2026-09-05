<?php

namespace App\GraphQL\Queries;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CoursesQuery
{
    /**
     * @param  array{status?: string, limit?: int, offset?: int}  $args
     * @return Collection<int, Course>
     */
    public function __invoke(null $_, array $args): Collection
    {
        $limit = max(1, min(100, (int) ($args['limit'] ?? 25)));
        $offset = max(0, (int) ($args['offset'] ?? 0));

        $query = Course::query()->orderByDesc('created_at');

        if (isset($args['status']) && $args['status'] !== '') {
            $query->where('status', (string) $args['status']);
        }

        return $query
            ->with(['chapters', 'courseLessons'])
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
