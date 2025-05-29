<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\Admin\AdProblemController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\ManageProblemController;

// Trang chủ
Route::get('/', [HomeController::class, 'index']);

// Authentication
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/forgot-password',    [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password',   [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password',    [AuthController::class, 'resetPassword'])->name('password.update');

// Public problems
Route::get('/problems',           [ProblemController::class, 'index'])->name('problems.index');
Route::get('/problems/{problem}', [ProblemController::class, 'show'])->name('problems.show');

// Submissions
Route::middleware(['auth'])->group(function () {
    Route::get('/problems/{problem}/submit', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/problems/{problem}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
});

// Problem Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/problems/{problem}/manage', [ManageProblemController::class, 'edit'])->name('problems.manage');
    Route::post('/problems/{problem}/update-description', [ManageProblemController::class, 'updateDescription'])->name('problems.update-description');
    Route::post('/problems/{problem}/update-content', [ManageProblemController::class, 'updateContent'])->name('problems.update-content');
    Route::post('/problems/{problem}/upload-testcases', [ManageProblemController::class, 'uploadTestCases'])->name('problems.upload-testcases');
    Route::get('/problems/{problem}/list-testcases', [ManageProblemController::class, 'listTestCases'])->name('problems.list-testcases');
    Route::get('/problems/{problem}/download-testcases', [ManageProblemController::class, 'downloadTestCases'])->name('problems.download-testcases');
});

// → Phần này: routes dành cho Admin, có middleware 'auth' và 'admin'
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
});
