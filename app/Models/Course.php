<?php

namespace App\Models;

use App\Enums\Semester;
use App\Enums\Year;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => Year::class,
        'semester' => Semester::class,
    ];

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get the user who created the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    /**
     * Get assessment events for this course.
     */
    public function assessmentEvents(): HasMany
    {
        return $this->hasMany(AssessmentEvent::class, 'course_id', 'id');
    }

    /**
     * Scope a query to only include courses eligible for assessment events:
     * non-empty year and semester.
     */
    public function scopeEligibleForAssessment($query)
    {
        return $query->whereNotNull('year')
            ->where('year', '!=', '')
            ->whereNotNull('semester')
            ->where('semester', '!=', '');
    }

    /**
     * Alias for scopeEligibleForAssessment.
     */
    public function scopeReadyForAssessment($query)
    {
        return $this->scopeEligibleForAssessment($query);
    }

    /**
     * Determine whether the course is eligible for assessment events.
     */
    public function isEligibleForAssessment(): bool
    {
        try {
            $year = $this->year;
            $semester = $this->semester;
        } catch (\ValueError) {
            return false;
        }

        $yearVal = $year instanceof Year ? $year->value : $year;
        $semesterVal = $semester instanceof Semester ? $semester->value : $semester;

        return ! empty($yearVal) && ! empty($semesterVal) && trim((string) $yearVal) !== '' && trim((string) $semesterVal) !== '';
    }
}
