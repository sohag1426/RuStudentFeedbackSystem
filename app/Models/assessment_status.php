<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assessment_status extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get the event
     */
    public function event()
    {
        return $this->belongsTo(assessment_event::class, 'event_id', 'id')->withDefault();
    }

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(assessment_event_student::class, 'student_id', 'student_id')->withDefault();
    }
}
