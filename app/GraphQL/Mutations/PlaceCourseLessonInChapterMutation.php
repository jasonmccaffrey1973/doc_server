<?php

namespace App\GraphQL\Mutations;

use App\Models\Chapter;
use App\Models\ChapterLesson;
use App\Models\CourseLesson;
use Illuminate\Support\Facades\Validator;

class PlaceCourseLessonInChapterMutation
{
    /**
     * @param  array{input: array{chapter_id: string, course_lesson_id: string, position: int}}  $args
     */
    public function __invoke(null $_, array $args): ChapterLesson
    {
        Validator::make($args['input'], [
            'chapter_id' => ['required', 'uuid', 'exists:chapters,id'],
            'course_lesson_id' => ['required', 'uuid', 'exists:course_lessons,id'],
            'position' => ['required', 'integer', 'min:1'],
        ])->validate();

        $chapter = Chapter::query()->findOrFail((string) $args['input']['chapter_id']);
        $courseLesson = CourseLesson::query()->findOrFail((string) $args['input']['course_lesson_id']);

        Validator::make([
            'course_id' => $courseLesson->course_id,
            'chapter_course_id' => $chapter->course_id,
        ], [
            'course_id' => ['same:chapter_course_id'],
        ], [
            'course_id.same' => 'The selected course lesson must belong to the same course as the chapter.',
        ])->validate();

        ChapterLesson::query()->updateOrCreate(
            [
                'chapter_id' => $chapter->id,
                'course_lesson_id' => $courseLesson->id,
            ],
            [
                'position' => (int) $args['input']['position'],
            ]
        );

        return ChapterLesson::query()
            ->where('chapter_id', $chapter->id)
            ->where('course_lesson_id', $courseLesson->id)
            ->firstOrFail();
    }
}
