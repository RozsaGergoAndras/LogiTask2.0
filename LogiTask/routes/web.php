<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Middleware\LogApiAccess;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//API route
Route::middleware('auth:sanctum')->middleware([LogApiAccess::class])->group(function () {
    Route::get('/api/task/', [TaskController::class, 'index'])->name('task.index');
    Route::get('/api/task/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('/api/task/{id}', [TaskController::class, 'update'])->name('task.update');

    Route::post('/api/file/upload', [TaskTypeController::class, 'uploadFile']);
    //Route::get('/api/file/download/{filename}', [TaskTypeController::class, 'downloadFile']); //LEGACY
    Route::get('/api/file/signed-url/{filename}', [TaskTypeController::class, 'getSignedUrl']);

    //userdata
    Route::get('/user', function (Request $request) {
        return $request->user(); // For getting the authenticated user info
    });
});

//API Lonin-out
Route::post('/api/login', [AuthenticatedSessionController::class, 'store']); // For logging in and getting a token
Route::middleware('auth:sanctum')->post('/api/logout', [AuthenticatedSessionController::class, 'destroy']);


require __DIR__.'/auth.php';
