# RU Student Feedback System - Execution Plans

This directory contains modular, step-by-step implementation plans to fix business logic errors, code logical errors, security vulnerabilities, and coding standards violations identified during the codebase audit.

## Plan Index

| File | Step | Focus Area | Status |
|---|---|---|---|
| [`01_critical_bugs_and_sql_syntax_fixes.md`](./01_critical_bugs_and_sql_syntax_fixes.md) | **Step 1** | SQL syntax error (`'=='`), file upload 500 crashes, storage file path leaks, import logic bug, time extension validation | ✅ Completed |
| [`02_security_authorization_and_authentication.md`](./02_security_authorization_and_authentication.md) | **Step 2** | Profile update authorization, password change security, student token & session verification, cURL error handling, SSRF / admin route protection | ✅ Completed |
| [`03_database_relationships_and_policy_fixes.md`](./03_database_relationships_and_policy_fixes.md) | **Step 3** | Broken model relationships (`assessment`, `assessment_status`), `User` fillable attributes, `ScoreService` transactions & counting, policy logic corrections, observer model_type fix | ✅ Completed |
| [`04_coding_standards_and_model_refactoring.md`](./04_coding_standards_and_model_refactoring.md) | **Step 4** | PSR-4 PascalCase model migration, `UserRole` enum relocation & cases, controller/mail typo fixes, clean up empty scaffold stubs, sync background jobs | ⏳ Pending Approval |
| [`05_route_cleanup_and_verification.md`](./05_route_cleanup_and_verification.md) | **Step 5** | Duplicate route group consolidation, feature test updates, comprehensive testing & verification | ⏳ Pending Approval |

---

## Execution Rule
We will work through these plans **one step at a time**, ensuring each step is fully executed, tested, and verified before moving to the next.
