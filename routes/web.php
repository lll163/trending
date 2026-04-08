<?php

use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
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

    // AI-GEN-BEGIN
    Route::get('/auth/github', [GitHubController::class, 'redirect'])->name('github.redirect');
    Route::get('/auth/github/callback', [GitHubController::class, 'callback'])->name('github.callback');
    Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    // AI-GEN-END
});

require __DIR__.'/auth.php';
