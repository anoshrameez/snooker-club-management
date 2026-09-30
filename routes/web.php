<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Authentication (Rate limited to prevent brute-force attacks)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Staff & Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Snooker Sessions Lifecycle
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{id}', [SessionController::class, 'show'])->name('sessions.show');
    Route::get('/sessions/{id}/live-status', [SessionController::class, 'liveStatus'])->name('sessions.liveStatus');
    Route::post('/sessions/{id}/rounds', [SessionController::class, 'updateRounds'])->name('sessions.updateRounds');
    Route::post('/sessions/{id}/payment', [SessionController::class, 'updatePayment'])->name('sessions.updatePayment');
    Route::post('/sessions/{id}/autosave', [SessionController::class, 'autosave'])->name('sessions.autosave');
    Route::post('/sessions/{id}/checkout', [SessionController::class, 'checkout'])->name('sessions.checkout');
    Route::post('/sessions/{id}/cancel', [SessionController::class, 'cancel'])->name('sessions.cancel');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // Tables view
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Admin-Only Routes
    Route::middleware(['admin'])->group(function () {
        // Table Management
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::put('/tables/{id}', [TableController::class, 'update'])->name('tables.update');

        // Club Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Staff / User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.password');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });
});
