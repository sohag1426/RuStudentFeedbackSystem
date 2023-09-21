<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assessment extends Model
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
        return $this->belongsTo(student_group_member::class, 'member_id', 'id')->withDefault();
    }


    /**
     * Get the student
     */
    public function question()
    {
        return $this->belongsTo(question::class, 'question_id', 'id')->withDefault();
    }
}
