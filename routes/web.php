<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseBuilderController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;
use App\Models\Course;

// Public Landing Route
Route::get('/', function () {
    $courses = Course::where('is_published', true)->with('instructor')->get();
    return view('landing', compact('courses'));
})->name('landing');

// Auth Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth Routes (All Users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Course Viewer & Player
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/play/{lesson?}', [CourseController::class, 'play'])->name('courses.play');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [CourseController::class, 'completeLesson'])->name('courses.complete-lesson');
    Route::post('/lessons/{lesson}/playback', [CourseController::class, 'savePlaybackPosition'])->name('lessons.playback');
    Route::get('/courses/{course}/certificate', [CourseController::class, 'certificate'])->name('courses.certificate');

    // Forums
    Route::get('/courses/{course}/forum', [ForumController::class, 'index'])->name('forums.index');
    Route::post('/courses/{course}/forum/topic', [ForumController::class, 'storeTopic'])->name('forums.store-topic');
    Route::get('/courses/{course}/forum/topic/{topic}', [ForumController::class, 'show'])->name('forums.show');
    Route::post('/courses/{course}/forum/topic/{topic}/reply', [ForumController::class, 'storeReply'])->name('forums.store-reply');

    // Quizzes
    Route::get('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/courses/{course}/quizzes/{quiz}/result/{attempt}', [QuizController::class, 'result'])->name('quizzes.result');
});

// Instructor Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/courses', [CourseBuilderController::class, 'index'])->name('builder.index');
    Route::get('/instructor/courses/create', [CourseBuilderController::class, 'create'])->name('builder.create');
    Route::post('/instructor/courses/store', [CourseBuilderController::class, 'store'])->name('builder.store');
    Route::get('/instructor/courses/{course}/edit', [CourseBuilderController::class, 'edit'])->name('builder.edit');
    Route::post('/instructor/courses/{course}/toggle-publish', [CourseBuilderController::class, 'togglePublish'])->name('builder.toggle-publish');
    Route::post('/instructor/courses/{course}/modules', [CourseBuilderController::class, 'addModule'])->name('builder.add-module');
    Route::post('/instructor/modules/{module}/lessons', [CourseBuilderController::class, 'addLesson'])->name('builder.add-lesson');
    Route::post('/instructor/quizzes/{quiz}/questions', [CourseBuilderController::class, 'addQuestion'])->name('builder.add-question');
    Route::post('/instructor/courses/{course}/reorder-modules', [CourseBuilderController::class, 'reorderModules'])->name('builder.reorder-modules');
    Route::post('/instructor/modules/{module}/reorder-lessons', [CourseBuilderController::class, 'reorderLessons'])->name('builder.reorder-lessons');
    
    // Ledger & Payouts
    Route::get('/instructor/dashboard', [InstructorController::class, 'dashboard'])->name('instructor.dashboard');
    Route::post('/instructor/payout-request', [InstructorController::class, 'requestPayout'])->name('instructor.request-payout');
});

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/payouts/{payout}/approve', [AdminController::class, 'approvePayout'])->name('admin.payouts.approve');
    Route::post('/admin/payouts/{payout}/reject', [AdminController::class, 'rejectPayout'])->name('admin.payouts.reject');
});
