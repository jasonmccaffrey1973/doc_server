<?php

namespace App\GraphQL\Mutations;

use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;

class DeleteChapterMutation
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(null $_, array $args): bool
    {
        Validator::make($args, [
            'id' => ['required', 'uuid'],
        ])->validate();

        return (bool) Chapter::query()->whereKey((string) $args['id'])->delete();
    }
}
