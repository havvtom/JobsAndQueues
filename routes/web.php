<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CustomJobsController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::get('/', [CustomJobsController::class, 'index'])->name('jobs.index');
Route::post('/jobs', [CustomJobsController::class, 'store'])->name('jobs.store');
Route::post('/jobs/cancel/{id}', [CustomJobsController::class, 'cancel'])->name('jobs.cancel');
Route::post('/jobs/retry/{id}', [CustomJobsController::class, 'retry'])->name('jobs.retry');
Route::post('/jobs/process-pending', [CustomJobsController::class, 'processPending'])->name('jobs.processPending');
Route::post('/jobs/updateJob', [CustomJobsController::class, 'updateJob'])->name('jobs.updateJob');


