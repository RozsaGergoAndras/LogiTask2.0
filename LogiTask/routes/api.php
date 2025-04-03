<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Regger;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatisticsController;
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

    //Route::get('/taskcontent/{id}', [TaskContentController::class, 'show']);
    Route::delete('/taskcontent/{id}', [TaskContentController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles/create', [RoleController::class, 'store']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::post('/file/upload', [TaskContentController::class, 'uploadFile']);
    //Route::get('/api/file/download/{filename}', [TaskContentController::class, 'downloadFile']); //LEGACY
    Route::get('/file/signed-url/{filename}', [TaskContentController::class, 'getSignedUrl']);

    //userdata
    Route::get('/user', function (Request $request) {
        return auth('sanctum')->user(); // For getting the authenticated user info
    });
    
    Route::post('/user/create', [Regger::class, 'store']);


    Route::get('/statistics/worker/compleated-task', [StatisticsController::class, 'GetWorkerActivity']);
    Route::get('/statistics/worker/avg-work-time', [StatisticsController::class, 'GetWorkerActivityByTime']);
    Route::get('/statistics/task/avg-task-time', [StatisticsController::class, 'GetAverageCompletionTime']);
    Route::get('/statistics/task/avg-task-time-by-type', [StatisticsController::class, 'GetAverageTaskCompletionTime']);
    Route::get('/statistics/task/task-count-by-role', [StatisticsController::class, 'GetTaskCountByRole']);
    Route::get('/statistics/api/most-active-api-users', [StatisticsController::class, 'GetMostActiveApiUsers']);
    Route::get('/statistics/api/usage', [StatisticsController::class, 'GetApiUsage']);
    Route::get('/statistics/api/requests', [StatisticsController::class, 'GetMostRequestedRoute']);
});

//API Lonin-out
Route::post('/login', [AuthenticatedSessionController::class, 'store']); // For logging in and getting a token
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);