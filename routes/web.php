<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [PagesController::class, 'home'])->name('home');

    // Apply rate limiting only in non-testing environments
    $checkRoute = Route::post('check', [PagesController::class, 'check']);
    if (app()->environment() !== 'testing') {
        $checkRoute->middleware('throttle:5,1');
    }
    $checkRoute->name('result.check');
});
