<?php

namespace App\GraphQL\Mutations;

use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class DeleteCourseMutation
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(null $_, array $args): bool
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
        ])->validate();

        return (bool) Course::query()->whereKey((string) $args['id'])->delete();
    }
}
