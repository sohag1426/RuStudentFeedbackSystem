<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessment_events';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get the teacher.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id')->withDefault();
    }

    /**
     * Get the course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->withDefault();
    }

    /**
     * Get the student group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'group_id', 'id')->withDefault();
    }

    /**
     * Get the user who created the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    /**
     * Get the students assigned to this event.
     */
    public function eventStudents(): HasMany
    {
        return $this->hasMany(AssessmentEventStudent::class, 'event_id', 'id');
    }

    /**
     * Get the assessment submissions.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'event_id', 'id');
    }

    /**
     * Get the assessment statuses.
     */
    public function assessmentStatuses(): HasMany
    {
        return $this->hasMany(AssessmentStatus::class, 'event_id', 'id');
    }

    /**
     * Get the detailed scores.
     */
    public function detailedScores(): HasMany
    {
        return $this->hasMany(DetailedScore::class, 'event_id', 'id');
    }

    /**
     * Get the comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'event_id', 'id');
    }

    /**
     * Calculate feedback completion percentage.
     */
    public function feedbackPercentage(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $eventId = $attributes['id'] ?? null;
                if (! $eventId) {
                    return 0;
                }

                $studentCount = AssessmentEventStudent::where('event_id', $eventId)->count();
                $done = AssessmentStatus::where('event_id', $eventId)->count();

                if ($studentCount == 0 || $done == 0) {
                    return 0;
                }

                return round((($done / $studentCount) * 100), 2);
            },
        );
    }
}
