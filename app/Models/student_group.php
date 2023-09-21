<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student_group extends Model
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
     * Get the members
     */
    public function members()
    {
        return $this->hasMany(student_group_member::class, 'group_id', 'id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }
}
