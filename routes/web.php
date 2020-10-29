<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\VoteController;
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

// Homepage
Route::get('/', [VoteController::class, 'index'])->name('home');
Route::get('/audit', [AuditController::class, 'index'])->name('audit');
Route::get('/audit/download', [AuditController::class, 'download'])->name('audit.download');

// Logout
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'find']);
Route::get('/login/verify', [LoginController::class, 'token'])->name('login.verify');
Route::post('/login/verify', [LoginController::class, 'verify']);
Route::post('/login/retry', [LoginController::class, 'retry'])->name('login.retry');

// Admin routes
Route::middleware('admin')->prefix('admin')->name('admin.')->group(static function () {
    // Home route
    Route::view('/', 'admin.index')->name('index');

    // Manage polls
    Route::prefix('polls')->group(static function () {
        // List
        Route::get('/', [PollController::class, 'index'])->name('polls.index');

        // Add
        Route::post('/create', [PollController::class, 'store'])->name('polls.create');

        // Download
        Route::post('/{poll}/download', [PollController::class, 'download'])->name('polls.download');
    });

    // Manage users
    Route::prefix('users')->group(static function () {
        // List
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');

        // Presence, Proxy and Monitor
        Route::post('/{user}/mark-present', [UserController::class, 'markPresent'])->name('users.present');
        Route::post('/{user}/proxy', [UserController::class, 'setProxy'])->name('users.proxy');
        Route::post('/{user}/monitor', [UserController::class, 'setMonitor'])->name('users.monitor');

        // Force a refresh
        Route::post('/refresh', [UserController::class, 'requestUpdate'])->name('users.refresh');
    });
});

// Monitor routes
Route::middleware('monitor')->prefix('monitor')->name('monitor.')->group(static function () {
    // Home route
    Route::get('/', [MonitorController::class, 'index'])->name('index');
});
