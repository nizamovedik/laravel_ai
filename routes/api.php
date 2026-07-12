<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskLabelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tasks')->controller(TaskController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{task}', 'show');
        Route::put('/{task}', 'update');
        Route::put('/{task}/status', 'updateStatus');
        Route::delete('/{task}', 'destroy');
    });

    Route::prefix('comments')->controller(CommentController::class)->group(function () {
        Route::get('/{type}/{id}', 'index');
        Route::post('/{type}/{id}', 'store');
        Route::put('/{comment}', 'update');
        Route::delete('/{comment}', 'destroy');
    });

    Route::prefix('projects')->controller(ProjectController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{project}', 'show');
        Route::put('/{project}', 'update');
        Route::delete('/{project}', 'destroy');
    });

    // Теги для задач
    Route::apiResource('task-labels', TaskLabelController::class);
});

// Авторизия
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
