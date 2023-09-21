<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student_group_member extends Model
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
     * Get the group
     */
    public function group()
    {
        return $this->belongsTo(student_group::class, 'group_id', 'id')->withDefault();
    }
}
