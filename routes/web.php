<?php

declare(strict_types=1);

use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'home'])->name('home');

// Apply rate limiting only in non-testing environments
$checkRoute = Route::post('check', [PagesController::class, 'check']);
if (app()->environment() !== 'testing') {
    $checkRoute->middleware('throttle:5,1');
}
$checkRoute->name('result.check');
