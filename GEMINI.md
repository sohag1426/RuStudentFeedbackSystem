# GEMINI.md

This file provides guidance to Gemini Code Assist and Gemini agents when working with code in this repository.

## Overview

Rajshahi University (RU) Student Feedback System: students anonymously rate teachers/courses through time-boxed "feedback events"; teachers and department chairs generate score reports. Laravel 10 (PHP ^8.0.2), Blade + Tailwind/Alpine (Breeze), Vite, Livewire 3, MySQL.

## Commands

```bash
composer install && npm install
npm run dev                          # Vite dev server
npm run build                        # production assets
php artisan serve

php artisan test                     # or vendor/bin/phpunit
php artisan test tests/Feature/Score/ScoreCalculationTest.php
php artisan test --filter test_score_generation_calculates_correct_event_and_detailed_scores

vendor/bin/pint                      # code style (Laravel Pint)
vendor/bin/pint --dirty              # only changed files
```

**Tests warning:** feature tests use `RefreshDatabase`, and the sqlite in-memory lines in `phpunit.xml` are commented out. There is no `.env.testing`, so tests run against whatever database `.env` points to and wipe it. Point tests at a throwaway database before running them.

Domain artisan commands (`app/Console/Commands`):
- `report:generate [--all] [--event=ID] [--date=YYYY-MM-DD]`: regenerates scores for running events and events that ended the previous day. The scheduler in `app/Console/Kernel.php` runs it daily.
- `app:add-admin`, `app:reset-admin-password`, `app:add-user`, `app:excel-to-users`, `app:add-department`, `updateOrCreateDepartments`

Production deploy steps are in `optimization.sh` (no-dev composer install, config/route/view/event caching, chown to www-data, `migrate --force`).

User docs are MkDocs in `documentation/` (student and teacher guides). They build into `public/docs`.

## Architecture

### Three separate authentication paths

1. **Staff (teachers, dept chairs, dept managers)**: the `web` guard with the `User` model and Breeze auth (`routes/auth.php`). Their routes sit under the `/admin` prefix with `auth` middleware in `routes/web.php`. Despite the prefix, these are department-staff pages, not system-admin pages. The role is a string column on `users` (`App\Enums\UserRole`: `teacher`, `DepartmentChair`, `DepartmentManager`, `admin`, `SuperAdmin`). Code usually compares raw strings such as `$user->role === 'DepartmentChair'`.
2. **System admins**: a separate `admin` guard with the `Admin` model (`admins` table) and middleware `auth.admin:admin` / `guest.admin:admin`. Routes are `/admin-login`, `/admin-dashboard`, `/admin-reports/*`, `/admin-analytics`. Cross-department reports are PDFs generated through dompdf (`AdminReportController`).
3. **Students**: no Laravel guard. `StudentLoginController` verifies the student ID and password against an external RU service (`config/verify.php`, env `VERIFY_URL`/`VERIFY_KEY`, base64-encoded JSON payload). It then stores a random token both in the cache (`student_token_{assessment_event_student_id}`, 120 min) and in the session. `AssessmentController::validateStudentSession()` checks the two copies match on every student request. Student routes are nested resources under `assessment_event_students/{id}/...`.

### Department scoping

Nearly every staff-side model carries `department_id`, and controllers filter by `$request->user()->department_id` by hand. There is no global scope. Policies (`app/Policies`) also check that department IDs match. New staff queries must apply this filter explicitly.

### Core domain flow

- `StudentGroup` (session, `Year` enum, `Semester` enum) → `StudentGroupMember` (a student ID). Session strings such as `2025-2026` come from `App\Services\SessionService`.
- `AssessmentEvent` = teacher + course + group + `start_time`/`stop_time`. When an event is created, the group's members are copied into `AssessmentEventStudent` rows (see `AssessmentEventController`). This per-event snapshot, not the group, decides who can log in and give feedback.
- A student submission writes one `Assessment` row per `Question` (score 1..`config('app.highest_score')`, default 5), plus an optional `Comment` and an `AssessmentStatus` row. **Anonymity by design:** `Assessment` and `Comment` do not store the student; only `AssessmentStatus` records that a student finished an event. Keep it that way.
- `Question`/`QuestionsGroup` are global and seeded (`database/seeders`). The source questionnaires are in `source-questions/`.
- `App\Services\ScoreService::generateScore()` is the single place scores are computed. It updates the event's `score`, `assessment_count` and `feedback_percentage`, rebuilds `DetailedScore` rows per question, and recomputes `group_average`/`group_highest`/`group_lowest` across all events in the same student group. It is called from `ScoreGenerateController` (authorized via `AssessmentEventPolicy::generateReport`), `report:generate` and `ScoreGenerateJob`.
- Excel import/export uses `spatie/simple-excel`: score downloads, student group export, the sample student sheet, bulk user import.

### Audit logging via observers

Observers registered in `EventServiceProvider` (User, Course, StudentGroup, StudentGroupMember, AssessmentEvent, AssessmentEventStudent) write rows to the `logs` table (`App\Models\Log`, not the Log facade) using `auth()->user()`. These logs appear at `/admin/change-logs`. Observers guard on `auth()->user()`, so model changes made from the CLI or from tests with no signed-in user skip logging.

### Migrations

The 2023 base migrations were generated from an existing schema (kitloong migrations-generator), with foreign keys in separate `add_foreign_keys_to_*` files. Later schema changes are incremental `update_*`/`add_*` migrations. Add new migrations; do not edit the generated ones.
