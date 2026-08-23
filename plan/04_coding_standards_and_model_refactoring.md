# Plan 04: Coding Standards & Model Refactoring

## Objectives
Bring codebase into compliance with PSR-12 and Laravel standard naming conventions, relocate and unify Enums, resolve controller/mail typos, clean up empty scaffolded boilerplate, and synchronize background jobs.

---

## Tasks

### 1. Refactor and Relocate `UserRole` Enum
- **Current File**: `app/Http/Controllers/Enum/UserRoles.php`
- **Target File**: `app/Enums/UserRole.php`
- **Problem**: The enum is misplaced under `Http/Controllers/Enum/`, named plural `UserRoles`, and contains outdated case values (`site_admin`, `department_admin`) not matching the roles in the database and application logic (`teacher`, `DepartmentChair`, `DepartmentManager`, `admin`, `SuperAdmin`).
- **Action**:
  - Create backed string enum in `app/Enums/UserRole.php`:
    ```php
    namespace App\Enums;

    enum UserRole: string
    {
        case TEACHER = 'teacher';
        case DEPARTMENT_CHAIR = 'DepartmentChair';
        case DEPARTMENT_MANAGER = 'DepartmentManager';
        case ADMIN = 'admin';
        case SUPER_ADMIN = 'SuperAdmin';

        public static function values(): array
        {
            return array_column(self::cases(), 'value');
        }
    }
    ```
  - Remove old file `app/Http/Controllers/Enum/UserRoles.php`.

---

### 2. Standardize Eloquent Models to PascalCase (PSR-4 Compliance)
- **Problem**: 13 out of 15 models use snake_case / lowercase class names and filenames:
  - `assessment.php` -> `Assessment.php`
  - `assessment_event.php` -> `AssessmentEvent.php`
  - `assessment_event_student.php` -> `AssessmentEventStudent.php`
  - `assessment_status.php` -> `AssessmentStatus.php`
  - `comment.php` -> `Comment.php`
  - `course.php` -> `Course.php`
  - `department.php` -> `Department.php`
  - `detailed_score.php` -> `DetailedScore.php`
  - `log.php` -> `Log.php`
  - `question.php` -> `Question.php`
  - `questions_group.php` -> `QuestionsGroup.php`
  - `student_group.php` -> `StudentGroup.php`
  - `student_group_member.php` -> `StudentGroupMember.php`
- **Action**:
  - Rename model files and class declarations to standard PascalCase.
  - Update all usages across Controllers, Observers, Policies, Providers, and Livewire components.
  - Provide class aliases for zero-regression backward compatibility if necessary.

---

### 3. Fix Typo in Controller and Mail Class
- **Files**:
  - `app/Http/Controllers/StudenLogoutController.php` -> Rename to `app/Http/Controllers/StudentLogoutController.php`
  - Update route definition in `routes/web.php`.
  - `app/Mail/AccountCreatedOnTeacherAssessmentSystem.php` (line 38): Fix subject string `'Account Created On Studen Feedback System'` -> `'Account Created On Student Feedback System'`.

---

### 4. Clean Up Empty Scaffolded Controllers
- **Files**:
  - `app/Http/Controllers/CommentController.php`
  - `app/Http/Controllers/DepartmentController.php`
  - `app/Http/Controllers/DetailedScoreController.php`
- **Problem**: Contain boilerplate methods with empty comments `//` that are not routed or utilized.
- **Action**:
  - Clean up unused empty stub files or implement relevant methods cleanly.

---

### 5. Synchronize / Clean Up `ScoreGenerateJob`
- **File**: `app/Jobs/ScoreGenerateJob.php`
- **Problem**: Contains an outdated duplicate implementation of the score generation logic that was subsequently moved to `ScoreService`.
- **Action**:
  - Refactor `ScoreGenerateJob::handle()` to delegate directly to `ScoreService::generateScore($this->assessment_event)`:
    ```php
    public function handle()
    {
        \App\Services\ScoreService::generateScore($this->assessment_event);
    }
    ```

---

## Verification Criteria
- [x] All models adhere to PSR-4 PascalCase naming and load cleanly without autoloader warnings.
- [x] `UserRole` enum is in `app/Enums/UserRole.php` and referenced across the codebase.
- [x] Typo in `StudentLogoutController` and mail subject are fixed.
- [x] `ScoreGenerateJob` delegates directly to `ScoreService`.
- [x] Codebase passes PHP linting (`php -l`) and code style inspection.
