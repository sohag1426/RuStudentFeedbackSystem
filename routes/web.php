<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentEventController;
use App\Http\Controllers\AssessmentEventStudentController;
use App\Http\Controllers\AssessmentStatusController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SampleExcel;
use App\Http\Controllers\ScoreDownloadController;
use App\Http\Controllers\ScoreGenerateController;
use App\Http\Controllers\ScreenShotController;
use App\Http\Controllers\StudenLogoutController;
use App\Http\Controllers\StudentGroupController;
use App\Http\Controllers\StudentGroupMemberController;
use App\Http\Controllers\StudentLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersProfileEditController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [StudentLoginController::class, 'create'])->name('student-login-form');
Route::post('/student-login', [StudentLoginController::class, 'store'])->name('student-login');
Route::resource('assessment_event_students.logout', StudenLogoutController::class)->only(['store']);
Route::resource('assessment_event_students.assessment_events', AssessmentController::class)
    ->only(['index', 'edit', 'update']);


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class)->only(['index']);
    Route::resource('users.profile', UsersProfileEditController::class)->only(['create', 'store']);
    Route::resource('courses', CourseController::class)->except(['show', 'destroy']);
    Route::resource('student_groups', StudentGroupController::class)->except(['show', 'destroy']);
    Route::resource('questions', QuestionController::class)->only(['index']);
    Route::resource('assessment_events.status', AssessmentStatusController::class)->only('index');
    Route::get('sample-excel', SampleExcel::class)->name('sample-excel');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('assessment_events', AssessmentEventController::class)->except(['show']);
    Route::resource('assessment_events.assessment_event_students', AssessmentEventStudentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('student_groups.student_group_members', StudentGroupMemberController::class)->shallow()->except(['show', 'edit', 'update']);
    Route::get('generate-score/{assessment_event}', ScoreGenerateController::class)->name('generate-score');
    Route::get('download-score/{assessment_event}', ScoreDownloadController::class)->name('download-score');
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
    Route::get('change-logs', [LogController::class, 'index'])->name('change-logs');
    Route::get('screenshot', ScreenShotController::class);
});

Route::middleware('guest.admin:admin')->group(function () {
    Route::get('admin-login', [AdminLoginController::class, 'create'])
        ->name('admin-login');
    Route::post('admin-login', [AdminLoginController::class, 'store']);
});

Route::middleware('auth.admin:admin')->group(function () {
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin-dashboard');
    Route::post('admin-logout', [AdminLoginController::class, 'destroy'])
        ->name('admin-logout');
});

require __DIR__ . '/auth.php';
