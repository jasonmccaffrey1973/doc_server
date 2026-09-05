<?php

namespace App\GraphQL\Queries;

use App\Models\ChapterLesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class ChapterLessonsQuery
{
    /**
     * @param  array{chapter_id: string}  $args
     * @return Collection<int, ChapterLesson>
     */
    public function __invoke(null $_, array $args): Collection
    {
        Validator::make($args, [
            'chapter_id' => ['required', 'uuid', 'exists:chapters,id'],
        ])->validate();

        return ChapterLesson::query()
            ->with(['courseLesson.lesson'])
            ->where('chapter_id', (string) $args['chapter_id'])
            ->orderBy('position')
            ->get();
    }
}
