<?php

namespace App\Models;

use App\Enums\Semester;
use App\Enums\Year;
use Carbon\Carbon;
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'feedback_percentage' => 0,
        'score' => 'undefined',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => Year::class,
        'semester' => Semester::class,
        'feedback_percentage' => 'float',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AssessmentEvent $event) {
            if ($event->group_id && (! $event->session || ! $event->year || ! $event->semester)) {
                $group = $event->group ?? StudentGroup::find($event->group_id);
                if ($group) {
                    $event->session = $event->session ?: $group->session;
                    $event->year = $event->year ?: ($group->year instanceof Year ? $group->year->value : $group->year);
                    $event->semester = $event->semester ?: ($group->semester instanceof Semester ? $group->semester->value : $group->semester);
                }
            }
        });

        static::updating(function (AssessmentEvent $event) {
            if ($event->isDirty('teacher_id') && $event->getOriginal('teacher_id') !== null) {
                $event->teacher_id = $event->getOriginal('teacher_id');
            }
            if ($event->isDirty('course_id') && $event->getOriginal('course_id') !== null) {
                $event->course_id = $event->getOriginal('course_id');
            }
            if ($event->isDirty('group_id') && $event->getOriginal('group_id') !== null) {
                $event->group_id = $event->getOriginal('group_id');
            }
            if ($event->isDirty('session') && $event->getOriginal('session') !== null) {
                $event->session = $event->getOriginal('session');
            }
            if ($event->isDirty('year') && $event->getOriginal('year') !== null) {
                $event->year = $event->getOriginal('year');
            }
            if ($event->isDirty('semester') && $event->getOriginal('semester') !== null) {
                $event->semester = $event->getOriginal('semester');
            }
        });
    }

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
     * Get the feedback completion percentage.
     */
    public function feedbackPercentage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (float) ($value ?? 0),
        );
    }

    /**
     * Scope a query to only include running assessment events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon|string|null  $now
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRunning($query, $now = null)
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();

        return $query->where('start_time', '<=', $now)
            ->where('stop_time', '>=', $now);
    }

    /**
     * Scope a query to only include assessment events that ended one day before.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon|string|null  $now
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEndedOneDayBefore($query, $now = null)
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();

        return $query->where('start_time', '<=', $now)
            ->where('stop_time', '<', $now)
            ->where('stop_time', '>=', $yesterdayStart);
    }

    /**
     * Scope a query to only include assessment events that are running or ended one day before.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon|string|null  $now
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRunningOrEndedOneDayBefore($query, $now = null)
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();

        return $query->where('start_time', '<=', $now)
            ->where(function ($q) use ($now, $yesterdayStart) {
                $q->where('stop_time', '>=', $now)
                    ->orWhere(function ($sub) use ($now, $yesterdayStart) {
                        $sub->where('stop_time', '<', $now)
                            ->where('stop_time', '>=', $yesterdayStart);
                    });
            });
    }
}
