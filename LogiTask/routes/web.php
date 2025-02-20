<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/api/task/', [Task::class, 'index'])->name('task.index');
    Route::get('/api/task/{id}', [Task::class, 'show'])->name('task.show');
    Route::post('/api/task/{id}', [Task::class, 'update'])->name('task.update');
});

Route::post('/api/login', [AuthenticatedSessionController::class, 'store']); // For logging in and getting a token
//userdata
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user(); // For getting the authenticated user info
});
 
//API Logout
Route::middleware('auth:sanctum')->post('/api/logout', [AuthenticatedSessionController::class, 'destroy']);


require __DIR__.'/auth.php';
