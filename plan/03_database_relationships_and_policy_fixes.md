# Plan 03: Database Relationships & Policy Fixes

## Objectives
Fix broken Eloquent relationships, repair incomplete mass-assignment fillable attributes, wrap score calculations in atomic database transactions, align evaluation counting semantics, fix policy permissions, and correct observer logging.

---

## Tasks

### 1. Remove Invalid Relationship from `assessment` Model
- **File**: `app/Models/assessment.php` (lines 38-41)
- **Problem**: `assessment` model defines:
  ```php
  public function student() {
      return $this->belongsTo(student_group_member::class, 'member_id', 'id')->withDefault();
  }
  ```
  `assessments` table has NO `member_id` column (evaluations are anonymous). Calling `$assessment->student` triggers SQL column not found errors or returns empty defaults.
- **Action**:
  - Remove the broken `student()` method from `assessment` model and document anonymous evaluation structure.

---

### 2. Fix Relationship Foreign Key on `assessment_status` Model
- **File**: `app/Models/assessment_status.php` (lines 38-41)
- **Problem**: `assessment_status` defines `student()` using `'member_id'`, but the table column is `student_id`.
- **Action**:
  - Update `student()` relationship:
    ```php
    public function student()
    {
        return $this->belongsTo(assessment_event_student::class, 'student_id', 'student_id')->withDefault();
    }
    ```

---

### 3. Update Fillable Attributes on `User` Model
- **File**: `app/Models/User.php` (lines 19-23)
- **Problem**: `$fillable` is restricted to `['name', 'email', 'password']`. Modifying or creating users with `internet_id`, `department_id`, `role`, `mobile`, `designation`, `department` silently ignores those fields when using mass assignment.
- **Action**:
  - Update `$fillable`:
    ```php
    protected $fillable = [
        'internet_id',
        'department_id',
        'role',
        'name',
        'email',
        'mobile',
        'designation',
        'department',
        'password',
    ];
    ```

---

### 4. Wrap `ScoreService` in DB Transaction & Align Student Evaluation Count
- **File**: `app/Services/ScoreService.php`
- **Problem**:
  1. `$assessment_event->assessment_count = $assessments_count;` sets assessment count to total question answer rows (e.g. 150 rows) instead of the number of students who completed the assessment (e.g. 10 students).
  2. The multi-step write process (deleting detailed scores, inserting scores, updating group metrics across all events) runs outside `DB::transaction(...)`. A crash halfway leaves broken data.
- **Action**:
  - Wrap all operations in `DB::transaction(function () use ($assessment_event) { ... })`.
  - Calculate `$distinct_student_count = assessment_status::where('event_id', $assessment_event->id)->count();` and store that as `$assessment_event->assessment_count`.

---

### 5. Fix `AssessmentEventPolicy` Permission Inconsistencies
- **File**: `app/Policies/AssessmentEventPolicy.php`
- **Problem**:
  1. `generateReport()` checks `assessment::where(...)->count() > 1`. If only 1 student evaluation was submitted, report generation is rejected.
  2. `downloadReport()` blocks `DepartmentChair` from downloading reports, even though `DepartmentChair` is authorized in `generateReport()`.
  3. `viewScore()` explicitly returns `false` for `DepartmentChair`.
- **Action**:
  - Change `generateReport()` threshold to `assessment::where('event_id', $assessmentEvent->id)->count() >= 1`.
  - Update `downloadReport()` to allow `DepartmentChair` of the same department:
    ```php
    public function downloadReport(User $user, assessment_event $assessmentEvent): bool
    {
        if ($user->department_id !== $assessmentEvent->department_id) {
            return false;
        }

        if ($user->role === 'DepartmentChair' || $user->id === $assessmentEvent->teacher_id) {
            return detailed_score::where('event_id', $assessmentEvent->id)->exists();
        }

        return false;
    }
    ```
  - Update `viewScore()` to allow both the teacher and their DepartmentChair.

---

### 6. Correct `model_type` in `AssessmentEventStudentObserver`
- **File**: `app/Observers/AssessmentEventStudentObserver.php` (line 39)
- **Problem**: `$log->model_type` is incorrectly hardcoded as `'App\Models\student_group_member'` instead of `'App\Models\assessment_event_student'`.
- **Action**:
  - Update to `$log->model_type = assessment_event_student::class;`.

---

## Verification Criteria
- [ ] `$assessment->student` no longer causes errors on non-existent column `member_id`.
- [ ] `$assessment_status->student` correctly resolves student record.
- [ ] `User::create([...])` correctly persists all user attributes.
- [ ] `ScoreService::generateScore` runs atomically inside a database transaction.
- [ ] `DepartmentChair` can generate and download reports for courses in their department.
- [ ] Observer deletion logs record correct model class.
