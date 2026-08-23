<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentEventStudent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessment_event_students';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get the assessment event.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(AssessmentEvent::class, 'event_id', 'id')->withDefault();
    }

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get the student group.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'group_id', 'id')->withDefault();
    }
}
