<?php

namespace App\GraphQL\Mutations;

use App\Models\ChapterLesson;
use Illuminate\Support\Facades\Validator;

class RemoveCourseLessonFromChapterMutation
{
    /**
     * @param  array{chapter_id: string, course_lesson_id: string}  $args
     */
    public function __invoke(null $_, array $args): bool
    {
        Validator::make($args, [
            'chapter_id' => ['required', 'uuid', 'exists:chapters,id'],
            'course_lesson_id' => ['required', 'uuid', 'exists:course_lessons,id'],
        ])->validate();

        return (bool) ChapterLesson::query()
            ->where('chapter_id', (string) $args['chapter_id'])
            ->where('course_lesson_id', (string) $args['course_lesson_id'])
            ->delete();
    }
}
