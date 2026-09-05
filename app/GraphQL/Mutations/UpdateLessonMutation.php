<?php

namespace App\GraphQL\Mutations;

use App\Models\Lesson;
use Illuminate\Support\Facades\Validator;

class UpdateLessonMutation
{
    /**
     * @param  array{id: string, input: array{title?: string, content?: array<string, mixed>}}  $args
     */
    public function __invoke(null $_, array $args): Lesson
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
            'input.title' => ['sometimes', 'string', 'max:255'],
            'input.content' => ['sometimes', 'array'],
        ])->validate();

        $lesson = Lesson::query()->findOrFail((string) $args['id']);

        $updatingContent = array_key_exists('content', $args['input']);
        $lesson->fill($args['input']);

        if ($updatingContent) {
            $lesson->content_version = $lesson->content_version + 1;
        }

        $lesson->save();

        return $lesson->fresh();
    }
}
