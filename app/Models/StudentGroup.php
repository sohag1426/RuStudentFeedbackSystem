<?php

namespace App\Models;

use App\Enums\Semester;
use App\Enums\Year;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGroup extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_groups';

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
        'session' => null,
    ];

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
     * Get the members of the student group.
     */
    public function members(): HasMany
    {
        return $this->hasMany(StudentGroupMember::class, 'group_id', 'id');
    }

    /**
     * Get the user who created the student group.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    /**
     * Get the assessment events for this student group.
     */
    public function assessmentEvents(): HasMany
    {
        return $this->hasMany(AssessmentEvent::class, 'group_id', 'id');
    }

    /**
     * Get the display name for the student group.
     */
    public function getDisplayNameAttribute(): string
    {
        $yearVal = $this->year instanceof Year ? $this->year->value : $this->year;
        $semesterVal = $this->semester instanceof Semester ? $this->semester->value : $this->semester;

        $parts = array_filter([
            $this->session,
            $yearVal,
            $semesterVal,
        ]);

        if (! empty($parts)) {
            return implode(', ', $parts);
        }

        return (string) $this->name;
    }

    /**
     * Scope a query to only include student groups eligible for assessment events:
     * non-empty session, year, semester, and at least 1 student member.
     */
    public function scopeEligibleForAssessment($query)
    {
        return $query->whereNotNull('session')
            ->where('session', '!=', '')
            ->whereNotNull('year')
            ->where('year', '!=', '')
            ->whereNotNull('semester')
            ->where('semester', '!=', '')
            ->has('members');
    }

    /**
     * Alias for scopeEligibleForAssessment.
     */
    public function scopeReadyForAssessment($query)
    {
        return $this->scopeEligibleForAssessment($query);
    }

    /**
     * Determine whether the student group is eligible for assessment events.
     */
    public function isEligibleForAssessment(): bool
    {
        $yearVal = $this->year instanceof Year ? $this->year->value : $this->year;
        $semesterVal = $this->semester instanceof Semester ? $this->semester->value : $this->semester;

        $hasSession = ! empty($this->session) && trim((string) $this->session) !== '';
        $hasYear = ! empty($yearVal) && trim((string) $yearVal) !== '';
        $hasSemester = ! empty($semesterVal) && trim((string) $semesterVal) !== '';
        $hasStudents = $this->relationLoaded('members') ? $this->members->isNotEmpty() : $this->members()->exists();

        return $hasSession && $hasYear && $hasSemester && $hasStudents;
    }

    /**
     * Alias for isEligibleForAssessment.
     */
    public function isReadyForAssessment(): bool
    {
        return $this->isEligibleForAssessment();
    }
}
