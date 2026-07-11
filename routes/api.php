<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskLabelController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

    // Теги для задач
    Route::apiResource('task-labels', TaskLabelController::class);
});

// Авторизия
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Неверные учетные данные'],
        ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Выход выполнен']);
})->middleware('auth:sanctum');
