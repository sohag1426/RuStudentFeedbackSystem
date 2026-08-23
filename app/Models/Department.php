<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get assessment events for this department.
     */
    public function events(): HasMany
    {
        return $this->hasMany(AssessmentEvent::class, 'department_id', 'id');
    }

    /**
     * Get courses belonging to this department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'department_id', 'id');
    }

    /**
     * Get student groups belonging to this department.
     */
    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class, 'department_id', 'id');
    }

    /**
     * Get users belonging to this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }
}
