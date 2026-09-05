<?php

namespace App\GraphQL\Mutations;

use App\Models\CourseLesson;
use App\Models\Lesson;
use Illuminate\Support\Facades\Validator;

class AddLessonToCourseMutation
{
    /**
     * @param  array{input: array{course_id: string, lesson_id: string}}  $args
     */
    public function __invoke(null $_, array $args): CourseLesson
    {
        Validator::make($args['input'], [
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
            'lesson_id' => ['required', 'uuid', 'exists:lessons,id'],
        ])->validate();

        $sourceLesson = Lesson::query()->findOrFail((string) $args['input']['lesson_id']);

        return CourseLesson::query()->firstOrCreate(
            [
                'course_id' => (string) $args['input']['course_id'],
                'lesson_id' => (string) $args['input']['lesson_id'],
            ],
            [
                'title' => $sourceLesson->title,
                'content' => $sourceLesson->content,
                'source_version' => $sourceLesson->content_version,
            ]
        );
    }
}
