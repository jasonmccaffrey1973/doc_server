<?php

namespace App\GraphQL\Mutations;

use App\Models\Lesson;
use Illuminate\Support\Facades\Validator;

class CreateLessonMutation
{
    /**
     * @param  array{input: array{title: string, content?: array<string, mixed>}}  $args
     */
    public function __invoke(null $_, array $args): Lesson
    {
        Validator::make($args['input'], [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'array'],
        ])->validate();

        return Lesson::query()->create([
            'title' => (string) $args['input']['title'],
            'content' => $args['input']['content'] ?? ['type' => 'doc', 'content' => []],
            'content_version' => 1,
        ]);
    }
}
