<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'position',
    ];

    /**
     * Get the course that owns this chapter.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get lesson placements for this chapter.
     */
    public function chapterLessons(): HasMany
    {
        return $this->hasMany(ChapterLesson::class)->orderBy('position');
    }

    /**
     * Get the course-specific lessons assigned to this chapter.
     */
    public function courseLessons(): BelongsToMany
    {
        return $this->belongsToMany(CourseLesson::class, 'chapter_lessons')
            ->withPivot(['position'])
            ->withTimestamps()
            ->orderBy('chapter_lessons.position');
    }
}
