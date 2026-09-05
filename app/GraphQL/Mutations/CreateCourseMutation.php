<?php

namespace App\GraphQL\Mutations;

use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class CreateCourseMutation
{
    /**
     * @param  array{input: array{title: string, description?: string, status?: string}}  $args
     */
    public function __invoke(null $_, array $args): Course
    {
        Validator::make($args['input'], [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
        ])->validate();

        return Course::query()->create([
            'title' => (string) $args['input']['title'],
            'description' => $args['input']['description'] ?? null,
            'status' => (string) ($args['input']['status'] ?? 'draft'),
        ]);
    }
}
