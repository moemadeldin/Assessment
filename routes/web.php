<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|View => view('welcome'));

Route::get('/test', fn (): string => dd('logged in'))
->middleware('auth')
->name('test');

Route::prefix('auth')
    ->middleware('guest')
    ->group(function (): void {
        Route::controller(RegisterController::class)->group(function (): void {
            Route::get('/register', 'create')->name('register');
            Route::post('/register', 'store')->name('register.post');
        });
    });
