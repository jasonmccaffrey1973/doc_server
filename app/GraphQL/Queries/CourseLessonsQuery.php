<?php

namespace App\GraphQL\Queries;

use App\Models\CourseLesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class CourseLessonsQuery
{
    /**
     * @param  array{course_id: string}  $args
     * @return Collection<int, CourseLesson>
     */
    public function __invoke(null $_, array $args): Collection
    {
        Validator::make($args, [
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
        ])->validate();

        return CourseLesson::query()
            ->with('lesson')
            ->where('course_id', (string) $args['course_id'])
            ->orderBy('created_at')
            ->get();
    }
}
