<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get the questions group.
     */
    public function questionsGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionsGroup::class, 'questions_group_id', 'id')->withDefault();
    }

    /**
     * Legacy snake_case relation alias.
     */
    public function questions_group(): BelongsTo
    {
        return $this->questionsGroup();
    }

    /**
     * Get assessments for this question.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'question_id', 'id');
    }

    /**
     * Get detailed scores for this question.
     */
    public function detailedScores(): HasMany
    {
        return $this->hasMany(DetailedScore::class, 'question_id', 'id');
    }
}
