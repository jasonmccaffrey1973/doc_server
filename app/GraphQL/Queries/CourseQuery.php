<?php

namespace App\GraphQL\Queries;

use App\Models\Course;

class CourseQuery
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(null $_, array $args): ?Course
    {
        return Course::query()
            ->with(['chapters.courseLessons', 'courseLessons.lesson'])
            ->find((string) $args['id']);
    }
}
