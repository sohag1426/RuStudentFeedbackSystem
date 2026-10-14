<?php

namespace App\Models;

use App\Enums\Semester;
use App\Enums\Year;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
     * Get the change logs for this course.
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'model');
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

    /**
     * Determine whether a specific column is editable (is null or empty string).
     */
    public function isColumnEditable(string $column): bool
    {
        $value = match ($column) {
            'year' => $this->year instanceof Year ? $this->year->value : $this->year,
            'semester' => $this->semester instanceof Semester ? $this->semester->value : $this->semester,
            default => $this->{$column} ?? null,
        };

        return is_null($value) || trim((string) $value) === '';
    }

    /**
     * Determine whether the course code is editable.
     */
    public function canEditCode(): bool
    {
        return $this->isColumnEditable('code');
    }

    /**
     * Determine whether the course name is editable.
     */
    public function canEditName(): bool
    {
        return $this->isColumnEditable('name');
    }

    /**
     * Determine whether the course year is editable.
     */
    public function canEditYear(): bool
    {
        return $this->isColumnEditable('year');
    }

    /**
     * Determine whether the course semester is editable.
     */
    public function canEditSemester(): bool
    {
        return $this->isColumnEditable('semester');
    }

    /**
     * Determine whether the course has any editable columns left.
     */
    public function hasEditableColumns(): bool
    {
        return $this->canEditCode() || $this->canEditName() || $this->canEditYear() || $this->canEditSemester();
    }
}
