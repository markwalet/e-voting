<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
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
    Route::view('/', 'admin.index')->name('home');

    // Manage polls
    Route::prefix('polls')->group(static function () {
        // List
        Route::get('/', [PollController::class, 'index'])->name('polls.index');

        // Add
        Route::post('/create', [PollController::class, 'store'])->name('polls.create');

        // Start and stop
        Route::post('/{poll}/open', [PollController::class, 'open'])->name('polls.open');
        Route::post('/{poll}/close', [PollController::class, 'close'])->name('polls.close');

        // Delete concepts
        Route::post('/{poll}/delete', [PollController::class, 'delete'])->name('polls.delete');
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
    });
});
