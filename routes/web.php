<?php

declare(strict_types=1);

use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'home'])->name('home');
Route::post('check', [PagesController::class, 'check'])
    ->middleware('throttle:5,1')
    ->name('result.check');
