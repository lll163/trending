<?php

use App\Http\Controllers\Admin\SubmissionReviewController;
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
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    // AI-GEN-END
});

// AI-GEN-BEGIN
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/submissions', [SubmissionReviewController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [SubmissionReviewController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/approve', [SubmissionReviewController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{submission}/reject', [SubmissionReviewController::class, 'reject'])->name('submissions.reject');
});
// AI-GEN-END

require __DIR__.'/auth.php';
