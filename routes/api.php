<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/tasks', [TaskController::class, 'store']); // ->middleware('auth');
Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->middleware('auth');
