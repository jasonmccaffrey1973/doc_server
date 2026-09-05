<?php

namespace App\GraphQL\Mutations;

use App\Models\CourseLesson;
use Illuminate\Support\Facades\Validator;

class UpdateCourseLessonMutation
{
    /**
     * @param  array{id: string, input: array{title?: string, content?: array<string, mixed>}}  $args
     */
    public function __invoke(null $_, array $args): CourseLesson
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
            'input.title' => ['sometimes', 'string', 'max:255'],
            'input.content' => ['sometimes', 'array'],
        ])->validate();

        $courseLesson = CourseLesson::query()->findOrFail((string) $args['id']);
        $courseLesson->fill($args['input']);
        $courseLesson->save();

        return $courseLesson->fresh();
    }
}
