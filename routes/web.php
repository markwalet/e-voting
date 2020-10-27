<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\ProxyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LoginController;
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
Route::get('/', static fn () => view('welcome'));

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'find']);
Route::get('/login/verify', [LoginController::class, 'token'])->name('login.verify');
Route::post('/login/verify', [LoginController::class, 'verify']);
Route::post('/login/retry', [LoginController::class, 'retry'])->name('login.retry');

// Admin routes
Route::middleware(['admin', 'can:admin', 'private'])
    ->prefix('admin')
    ->name('admin.')
    ->group(static function () {
        // Home route
        Route::get('/', [AdminController::class, 'index'])->name('home');

        // Manage polls
        Route::prefix('polls')->group(static function () {
            // List
            Route::get('/', [PollController::class, 'index'])->name('polls.index');

            // Add
            Route::post('/create', [PollController::class, 'store'])->name('polls.create');

            // Start and stop
            Route::post('/{poll}/start', [PollController::class, 'start'])->name('polls.start');
            Route::post('/{poll}/stop', [PollController::class, 'stop'])->name('polls.stop');
        });

        // Manage users
        Route::prefix('users')->group(static function () {
            // List
            Route::get('/', [UserController::class, 'index'])->name('users.index');

            // Add
            Route::get('/present', [UserController::class, 'markPresent'])->name('users.mark-present');
            Route::post('/absent', [UserController::class, 'markAbsent'])->name('users.mark-absent');
        });

        // Manage users
        Route::prefix('proxies')->group(static function () {
            // List
            Route::get('/', [ProxyController::class, 'index'])->name('auths.index');

            // Add
            Route::get('/remove', [ProxyController::class, 'remove'])->name('auths.remove');
            Route::post('/add', [ProxyController::class, 'add'])->name('auths.add');
        });
    });
