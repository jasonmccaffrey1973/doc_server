<?php

namespace App\GraphQL\Courses\Queries;

use App\Models\Lesson;

class LessonQuery
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(null $_, array $args): ?Lesson
    {
        return Lesson::query()->find((string) $args['id']);
    }
}
