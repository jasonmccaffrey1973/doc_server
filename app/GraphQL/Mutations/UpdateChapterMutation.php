<?php

namespace App\GraphQL\Mutations;

use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class UpdateChapterMutation
{
    /**
     * @param  array{id: string, input: array{title?: string, description?: string, position?: int}}  $args
     */
    public function __invoke(null $_, array $args): Chapter
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
            'input.title' => ['sometimes', 'string', 'max:255'],
            'input.description' => ['nullable', 'string'],
            'input.position' => ['sometimes', 'integer', 'min:1'],
        ])->validate();

        $chapter = Chapter::query()->findOrFail((string) $args['id']);
        $chapter->fill($args['input']);
        $chapter->save();

        return $chapter->fresh();
    }
}
