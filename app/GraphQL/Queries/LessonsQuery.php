<?php

namespace App\GraphQL\Queries;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

class LessonsQuery
{
    /**
     * @param  array{search?: string, limit?: int, offset?: int}  $args
     * @return Collection<int, Lesson>
     */
    public function __invoke(null $_, array $args): Collection
    {
        $limit = max(1, min(100, (int) ($args['limit'] ?? 25)));
        $offset = max(0, (int) ($args['offset'] ?? 0));

        $query = Lesson::query()->orderByDesc('created_at');

        if (isset($args['search']) && $args['search'] !== '') {
            $query->where('title', 'like', '%'.(string) $args['search'].'%');
        }

        return $query
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
