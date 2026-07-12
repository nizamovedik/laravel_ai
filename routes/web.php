<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [PageController::class, 'login'])->name('login');

// Все остальные страницы — через SPA (только для авторизованных)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
