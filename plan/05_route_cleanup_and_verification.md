# Plan 05: Route Cleanup & Comprehensive Verification

## Objectives
Consolidate route groups, configure proper middleware groupings, update test suite to test real application features, and run end-to-end verification.

---

## Tasks

### 1. Consolidate Route Groups in `routes/web.php`
- **File**: `routes/web.php` (lines 49-71)
- **Problem**: There are two separate duplicate `Route::prefix('admin')->middleware(['auth'])->group(...)` blocks.
- **Action**:
  - Consolidate all teacher/department-admin routes into a single clean group.
  - Separate developer tools (`logs`, `screenshot`) into an admin-only middleware protected block.
  - Update route name for `StudentLogoutController`.

---

### 2. Update Feature Tests for Current Authentication Model
- **Files**: `tests/Feature/Auth/*`
- **Problem**: The existing test suite was generated from default Laravel Breeze (testing standard email/password registration, password reset tokens, email verification links) which are disabled in this project because authentication uses custom university `internet_id` credentials.
- **Action**:
  - Update test cases to test the actual application features:
    - Teacher/Department Admin authentication via `internet_id`.
    - Admin portal login via `admin-login`.
    - Student login and token generation.
    - Course management CRUD.
    - Student group and member import.
    - Assessment event lifecycle and score generation.

---

### 3. Comprehensive Test Suite & Static Analysis
- Run PHPUnit / Pest tests to ensure all tests pass green.
- Run route check: `php artisan route:list`.
- Run config/cache checks: `php artisan config:clear`, `php artisan route:clear`.
- Verify database migrations and schema consistency.

---

## Verification Criteria
- [ ] `php artisan route:list` shows no route conflicts, invalid controller references, or missing actions.
- [ ] `vendor/bin/phpunit` runs with 100% passing tests (0 failures, 0 errors).
- [ ] Complete end-to-end flows (Student feedback submission, Teacher course management, Admin reporting) operate flawlessly.
