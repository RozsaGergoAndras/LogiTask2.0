<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Regger;
use App\Http\Controllers\TaskContentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Middleware\LogApiAccess;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

//API route
Route::middleware('auth:sanctum')->middleware([LogApiAccess::class])->group(function () {
    Route::get('/task', [TaskController::class, 'index'])->name('task.index');
    Route::get('/task/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('/task/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::post('/task/create', [TaskController::class, 'store'])->name('task.store');

    Route::get('/tasktype', [TaskTypeController::class, 'index']);
    Route::get('/tasktype/{id}', [TaskTypeController::class, 'show']);
    Route::post('/tasktype/{id}', [TaskTypeController::class, 'update']);
    Route::post('/tasktype/create', [TaskTypeController::class, 'store']);
    Route::delete('/tasktype/{id}', [TaskTypeController::class, 'destroy']);

    Route::post('/file/upload', [TaskContentController::class, 'uploadFile']);
    //Route::get('/api/file/download/{filename}', [TaskContentController::class, 'downloadFile']); //LEGACY
    Route::get('/file/signed-url/{filename}', [TaskContentController::class, 'getSignedUrl']);

    //userdata
    Route::get('/user', function (Request $request) {
        return auth('sanctum')->user(); // For getting the authenticated user info
    });

    Route::post('/user/create', [Regger::class, 'store']);
});

//API Lonin-out
Route::post('/login', [AuthenticatedSessionController::class, 'store']); // For logging in and getting a token
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);