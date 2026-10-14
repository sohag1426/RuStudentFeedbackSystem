<?php

namespace App\Policies;

use App\Models\AssessmentEvent;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        if ($user->department_id == $course->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the course code.
     * Code can only be edited if it is currently null or empty.
     */
    public function updateCode(User $user, Course $course): bool
    {
        if ($user->department_id != $course->department_id) {
            return false;
        }

        return is_null($course->code) || trim((string) $course->code) === '';
    }

    /**
     * Determine whether the user can update the course name.
     * Name can only be edited if it is currently null or empty.
     */
    public function updateName(User $user, Course $course): bool
    {
        if ($user->department_id != $course->department_id) {
            return false;
        }

        return is_null($course->name) || trim((string) $course->name) === '';
    }

    /**
     * Determine whether the user can update the course year.
     * Year can only be edited if it is currently null or empty.
     */
    public function updateYear(User $user, Course $course): bool
    {
        if ($user->department_id != $course->department_id) {
            return false;
        }

        $year = $course->year instanceof \App\Enums\Year ? $course->year->value : $course->year;

        return is_null($year) || trim((string) $year) === '';
    }

    /**
     * Determine whether the user can update the course semester.
     * Semester can only be edited if it is currently null or empty.
     */
    public function updateSemester(User $user, Course $course): bool
    {
        if ($user->department_id != $course->department_id) {
            return false;
        }

        $semester = $course->semester instanceof \App\Enums\Semester ? $course->semester->value : $course->semester;

        return is_null($semester) || trim((string) $semester) === '';
    }

    /**
     * Determine whether a specific column can be updated.
     */
    public function updateColumn(User $user, Course $course, string $column): bool
    {
        return match ($column) {
            'code' => $this->updateCode($user, $course),
            'name' => $this->updateName($user, $course),
            'year' => $this->updateYear($user, $course),
            'semester' => $this->updateSemester($user, $course),
            default => false,
        };
    }

    /**
     * Determine whether the user can update the model.
     * Allowed only if in the same department and at least one column is editable.
     */
    public function update(User $user, Course $course): bool
    {
        if ($user->department_id != $course->department_id) {
            return false;
        }

        return $this->updateCode($user, $course)
            || $this->updateName($user, $course)
            || $this->updateYear($user, $course)
            || $this->updateSemester($user, $course);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        if (AssessmentEvent::where('department_id', $user->department_id)->where('course_id', $course->id)->exists()) {
            return false;
        }

        if ($user->department_id == $course->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return false;
    }
}
