<?php

namespace App\GraphQL\Mutations;

use App\Models\Lesson;
use Illuminate\Support\Facades\Validator;

class DeleteLessonMutation
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(null $_, array $args): bool
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
        ])->validate();

        return (bool) Lesson::query()->whereKey((string) $args['id'])->delete();
    }
}
