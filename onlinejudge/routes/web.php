<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\SubmissionController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\ManageProblemController;
use App\Http\Controllers\AccountController;

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
Route::middleware(['auth'])->prefix('manage')->name('manage.')->group(function () {
    // Problem CRUD
    Route::resource('problems', ManageProblemController::class);
    
    // Content Management
    Route::get('/problems/{problem}/content', [ManageProblemController::class, 'content'])->name('problems.content');
    Route::post('/problems/{problem}/content', [ManageProblemController::class, 'updateContent'])->name('problems.content.update');
    
    // Test Cases Management
    Route::get('/problems/{problem}/testcases', [ManageProblemController::class, 'testcases'])->name('problems.testcases');
    Route::post('/problems/{problem}/testcases', [ManageProblemController::class, 'storeTestCase'])->name('problems.testcases.store');
    Route::get('/problems/{problem}/testcases/download', [ManageProblemController::class, 'downloadTestCases'])->name('problems.testcases.download');
    Route::delete('/problems/{problem}/testcases', [ManageProblemController::class, 'destroyTestCase'])->name('problems.testcases.destroy');
});

// Legacy Problem Management Routes (keeping for backward compatibility)
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
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Problem Management Routes
    Route::prefix('manage/problems')->name('manage.problems.')->group(function () {
        Route::get('/', [ManageProblemController::class, 'index'])->name('index');
        Route::get('/create', [ManageProblemController::class, 'create'])->name('create');
        Route::post('/', [ManageProblemController::class, 'store'])->name('store');
        Route::get('/{problem}', [ManageProblemController::class, 'show'])->name('show');
        Route::get('/{problem}/edit', [ManageProblemController::class, 'edit'])->name('edit');
        Route::put('/{problem}', [ManageProblemController::class, 'update'])->name('update');
        Route::delete('/{problem}', [ManageProblemController::class, 'destroy'])->name('destroy');
        
        // Content Management
        Route::get('/{problem}/content', [ManageProblemController::class, 'content'])->name('content');
        Route::put('/{problem}/content', [ManageProblemController::class, 'updateContent'])->name('updateContent');
        
        // Test Cases Management
        Route::get('/{problem}/testcases', [ManageProblemController::class, 'testcases'])->name('testcases');
        Route::post('/{problem}/testcases', [ManageProblemController::class, 'storeTestCase'])->name('testcases.store');
        Route::get('/{problem}/testcases/download', [ManageProblemController::class, 'downloadTestCases'])->name('testcases.download');
        Route::delete('/{problem}/testcases', [ManageProblemController::class, 'destroyTestCase'])->name('testcases.destroy');
        Route::delete('/{problem}/testcases/{testId}', [ManageProblemController::class, 'destroySingleTestCase'])->name('testcases.destroy.single');
    });
});

// Account Management Routes
Route::prefix('manage/accounts')->name('manage.accounts.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/create', [AccountController::class, 'create'])->name('create');
    Route::post('/create', [AccountController::class, 'create'])->name('create');
    Route::get('/{id}', [AccountController::class, 'details'])->name('details');
    
    // Tab routes
    Route::get('/{id}/information', [AccountController::class, 'information'])->name('information');
    Route::get('/{id}/password', [AccountController::class, 'password'])->name('password');
    Route::get('/{id}/settings', [AccountController::class, 'settings'])->name('settings');
    Route::get('/{id}/bio', [AccountController::class, 'bio'])->name('bio');
    
    // Action routes
    Route::put('/{id}/information', [AccountController::class, 'updateInformation'])->name('update-information');
    Route::put('/{id}/password', [AccountController::class, 'changePassword'])->name('change-password');
    Route::put('/{id}/admin', [AccountController::class, 'setAdmin'])->name('set-admin');
    Route::put('/{id}/bio', [AccountController::class, 'updateBio'])->name('update-bio');
});
