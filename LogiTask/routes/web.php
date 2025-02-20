<?php

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
Route::middleware('auth')->middleware([LogApiAccess::class])->group(function () {
    Route::get('/api/task/{id}', [Task::class, 'show'])->name('task.show');
    Route::post('/api/task/{id}', [Task::class, 'update'])->name('task.update');
});


require __DIR__.'/auth.php';
