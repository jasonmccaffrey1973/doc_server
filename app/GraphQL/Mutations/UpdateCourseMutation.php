<?php

namespace App\GraphQL\Mutations;

use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class UpdateCourseMutation
{
    /**
     * @param  array{id: string, input: array{title?: string, description?: string, status?: string}}  $args
     */
    public function __invoke(null $_, array $args): Course
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
            'input.title' => ['sometimes', 'string', 'max:255'],
            'input.description' => ['nullable', 'string'],
            'input.status' => ['sometimes', 'string', 'in:draft,published,archived'],
        ])->validate();

        $course = Course::query()->findOrFail((string) $args['id']);

        $course->fill($args['input']);
        $course->save();

        return $course->fresh();
    }
}
