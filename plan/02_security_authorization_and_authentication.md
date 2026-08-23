# Plan 02: Security, Authorization & Authentication

## Objectives
Harden user authorization, fix student session token validation, eliminate unhandled role crashes in password change, prevent SSRF, and safely handle external API authentication.

---

## Tasks

### 1. Add User Ownership Authorization & Unique Email Validation in Profile Update
- **File**: `app/Http/Controllers/UsersProfileEditController.php`
- **Problem**: `store()` method does not check if the authenticated user owns the profile being updated, allowing IDOR (Insecure Direct Object Reference). It also lacks a unique check on email ignoring current user ID.
- **Action**:
  - Add authorization check in `store()`:
    ```php
    if ($user->id !== $request->user()->id) {
        abort(403, 'Unauthorized profile update.');
    }
    ```
  - Validate email uniqueness:
    ```php
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'mobile' => ['required', 'string', 'max:30'],
    ]);
    ```

---

### 2. Fix Unhandled Roles and Require Current Password in Password Change
- **File**: `app/Http/Controllers/ChangePasswordController.php`
- **Problem**: `create()` uses `match ($request->user()->role)` with only `'admin'` and `'teacher'`. Users with role `'DepartmentChair'` or `'DepartmentManager'` get raw string `'Not Found'`. `store()` allows changing password without verifying current password.
- **Action**:
  - Update `create()`:
    ```php
    return match ($request->user()->role) {
        'admin', 'SuperAdmin' => view('admin.change-password'),
        'teacher', 'DepartmentChair', 'DepartmentManager' => view('teacher.change-password'),
        default => view('teacher.change-password'),
    };
    ```
  - In `store()`, add `current_password` verification:
    ```php
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', Password::min(8)],
    ]);
    ```

---

### 3. Harden Student Authentication & Cache Token Validation
- **Files**:
  - `app/Http/Controllers/StudentLoginController.php`
  - `app/Http/Controllers/AssessmentController.php`
  - `app/Http/Controllers/StudenLogoutController.php`
- **Problem**: `StudentLoginController::store` generates a token and stores it in cache by student ID. `AssessmentController` only checks if *any* value exists in cache (`Cache::get($id, false)`), without checking if the client session actually has that token.
- **Action**:
  - Store the generated session token in the student's session:
    ```php
    $sessionToken = Str::random(40);
    $request->session()->put('student_auth_token_' . $assessment_event_student->id, $sessionToken);
    Cache::put('student_token_' . $assessment_event_student->id, $sessionToken, now()->addMinutes(120));
    ```
  - In `AssessmentController`, check that the session token matches the cache token:
    ```php
    $sessionToken = $request->session()->get('student_auth_token_' . $assessment_event_student->id);
    $cachedToken = Cache::get('student_token_' . $assessment_event_student->id);
    if (!$sessionToken || !$cachedToken || !hash_equals($cachedToken, $sessionToken)) {
        return redirect()->route('student-login-form')->with('info', 'Session expired. Please log in again.');
    }
    ```

---

### 4. Replace Raw cURL with Laravel HTTP Client & Error Handling
- **File**: `app/Http/Controllers/StudentLoginController.php` (`verify()`)
- **Problem**: Raw `curl_*` functions are used. If cURL fails, it echoes the error string directly to the HTTP response, corrupting output headers, and causes a null array index exception on `$verify['error_code']`.
- **Action**:
  - Use `Illuminate\Support\Facades\Http`:
    ```php
    public static function verify(string $user, string $password): array
    {
        try {
            $payload = base64_encode(json_encode([
                'ru_user' => $user,
                'ru_pass' => $password,
                'key'     => config('verify.key'),
            ]));

            $response = Http::timeout(10)
                ->withBody($payload, 'text/plain')
                ->post(config('verify.url'));

            if ($response->successful()) {
                $decoded = json_decode(base64_decode($response->body()), true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Student verification API error: ' . $e->getMessage());
        }

        return ['error_code' => 1, 'message' => 'Verification service unavailable'];
    }
    ```

---

### 5. Protect System Logs & ScreenShot Endpoints from SSRF / Unauthorized Access
- **Files**:
  - `app/Http/Controllers/ScreenShotController.php`
  - `routes/web.php`
- **Problem**: `/admin/logs` (Laravel log viewer) and `/admin/screenshot` are accessible by any authenticated user (e.g. standard teacher) because they are under `middleware(['auth'])` without role restrictions. `ScreenShotController` allows arbitrary external URL navigation.
- **Action**:
  - Restrict `/admin/logs` and `/admin/screenshot` behind admin role middleware or admin guard.
  - In `ScreenShotController`, validate that `page` URL only matches allowed internal/app host domains (`config('app.url')`) and return a proper HTTP response.

---

## Verification Criteria
- [ ] Non-owner user cannot update another user's profile.
- [ ] DepartmentChair and DepartmentManager can access and update their passwords.
- [ ] Student session requires both valid session token and cache token to view or submit feedback.
- [ ] External API downtime returns structured error array without corrupting HTTP output headers.
- [ ] Sensitive developer routes (`logs`, `screenshot`) require Admin privileges.
