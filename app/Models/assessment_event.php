<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assessment_event extends Model
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
     * Get the teacher
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id')->withDefault();
    }

    /**
     * Get the course
     */
    public function course()
    {
        return $this->belongsTo(course::class, 'course_id', 'id')->withDefault();
    }

    /**
     * Get the group
     */
    public function group()
    {
        return $this->belongsTo(student_group::class, 'group_id', 'id')->withDefault();
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function feedbackPercentage(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {

                $studentCount = assessment_event_student::where('event_id', $attributes['id'])->count();
                $done = assessment_status::where('event_id', $attributes['id'])->count();

                if ($studentCount == 0) {
                    return 0;
                }

                if ($done == 0) {
                    return 0;
                }

                return round((($done / $studentCount) * 100), 2);
            },
        );
    }
}
