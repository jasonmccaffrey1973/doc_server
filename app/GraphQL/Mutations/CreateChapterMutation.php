<?php

namespace App\GraphQL\Mutations;

use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class CreateChapterMutation
{
    /**
     * @param  array{input: array{course_id: string, title: string, description?: string, position: int}}  $args
     */
    public function __invoke(null $_, array $args): Chapter
    {
        Validator::make($args['input'], [
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'position' => ['required', 'integer', 'min:1'],
        ])->validate();

        return Chapter::query()->create([
            'course_id' => (string) $args['input']['course_id'],
            'title' => (string) $args['input']['title'],
            'description' => $args['input']['description'] ?? null,
            'position' => (int) $args['input']['position'],
        ]);
    }
}
