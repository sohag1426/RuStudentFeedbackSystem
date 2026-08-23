# Plan 01: Critical Bugs & SQL Syntax Fixes

## Objectives
Fix immediate runtime crashes, SQL syntax errors, uncaught file upload exceptions, disk leakage, and faulty import loop logic.

---

## Tasks

### 1. Fix SQL Syntax Error with `'=='` Operator
- **File**: `app/Http/Controllers/CourseController.php` (lines 94 & 100)
- **Problem**: `course::where('department_id', ...)->where('code', '==', $request->code)` passes `'=='` directly into SQL, generating `WHERE code == ?` which causes a fatal SQL syntax error in MySQL on course update.
- **Action**:
  - Replace `'=='` with `'='` or simplify with:
    ```php
    $request->validate([
        'code' => ['required', 'string', Rule::unique('courses')->where('department_id', $request->user()->department_id)->ignore($course->id)],
        'name' => ['required', 'string', Rule::unique('courses')->where('department_id', $request->user()->department_id)->ignore($course->id)],
    ]);
    ```

---

### 2. Fix Fatal 500 on Missing File Uploads
- **Files**:
  - `app/Http/Controllers/AssessmentEventStudentController.php` (line 53)
  - `app/Http/Controllers/StudentGroupMemberController.php` (line 44)
- **Problem**: Validation defines `'excel_file' => 'mimes:xlsx'` without `'required'`. Submitting an empty form causes `$request->file('excel_file')->store(...)` to crash with `Call to a member function store() on null`.
- **Action**:
  - Update validation rules in both controllers:
    ```php
    $request->validate([
        'excel_file' => ['required', 'file', 'mimes:xlsx,xls'],
    ]);
    ```

---

### 3. Fix Storage Path Deletion Bug (Disk Leak)
- **Files**:
  - `app/Http/Controllers/AssessmentEventStudentController.php` (line 78)
  - `app/Http/Controllers/StudentGroupMemberController.php` (line 74)
- **Problem**: Code calls `Storage::delete($path)` where `$path = Storage::path($file)`. `Storage::delete` expects disk-relative paths (`$file`), not absolute filesystem paths (`$path`), failing to delete temporary uploaded Excel files.
- **Action**:
  - Change to `Storage::delete($file);` in both controllers.

---

### 4. Fix Student Group Member Import Logic Bug
- **File**: `app/Http/Controllers/StudentGroupMemberController.php` (lines 64-71)
- **Problem**:
  ```php
  if (student_group_member::where('group_id', $student_group->id)->where('student_id', $row['student_id'])->exists()) {
      continue;
  }
  student_group_member::updateOrCreate(...);
  ```
  The `if (exists) continue;` check prevents existing members from having their names updated when re-uploading an updated Excel roster, and makes `updateOrCreate` dead code.
- **Action**:
  - Remove redundant `if exists continue` check so `updateOrCreate` can insert new members and update existing members cleanly:
    ```php
    student_group_member::updateOrCreate(
        [
            'department_id' => $request->user()->department_id,
            'group_id' => $student_group->id,
            'student_id' => trim((string) $row['student_id']),
        ],
        ['name' => trim($row['name'])]
    );
    ```

---

### 5. Add Stop Time Boundary Validation on Event Extension
- **File**: `app/Http/Controllers/AssessmentEventTimeExtendController.php` (lines 25-42)
- **Problem**: No check exists to ensure the extended `stop_time` is after `start_time` or in the future.
- **Action**:
  - Validate that the new `stop_time` is greater than `$assessment_event->start_time` and greater than `now('Asia/Dhaka')`:
    ```php
    if ($stop_time->lessThanOrEqualTo($assessment_event->start_time)) {
        return back()->with('info', 'Stop time must be after the start time.');
    }
    if ($stop_time->lessThan(Carbon::now('Asia/Dhaka'))) {
        return back()->with('info', 'Stop time must be in the future.');
    }
    ```

---

## Verification Criteria
- [x] Course updates with duplicate check succeed without SQL syntax errors.
- [x] Uploading without a file in both event students and group members returns a validation error instead of 500 error.
- [x] Temporary files stored in `storage/app/group_members/` are cleanly deleted after import.
- [x] Group member re-import properly updates member names.
- [x] Assessment event time extension prevents setting invalid/past stop times.
