<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\MagazineIssueController;
use App\Http\Controllers\Admin\ReposSnapshotController;
use App\Http\Controllers\Admin\SubmissionReviewController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

// AI-GEN-BEGIN
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
Route::get('/rankings/{ranking_key}', [RankingController::class, 'index'])
    ->where('ranking_key', '[a-z0-9_]+')
    ->name('rankings.key');
// AI-GEN-END

// AI-GEN-BEGIN
Route::get('/repositories', [RepositoryController::class, 'index'])->name('repositories.index');
Route::get('/repositories/{owner}/{repo}', [RepositoryController::class, 'show'])
    ->where(['owner' => '[a-z0-9-]{1,39}', 'repo' => '[a-z0-9._-]{1,100}'])
    ->name('repositories.show');
// AI-GEN-END

// AI-GEN-BEGIN
Route::get('/magazines', [MagazineController::class, 'index'])->name('magazines.index');
Route::get('/magazines/{issue_code}/{article_slug}', [MagazineController::class, 'article'])
    ->where(['issue_code' => '[a-z0-9]+(?:-[a-z0-9]+)*', 'article_slug' => '[a-z0-9]+(?:-[a-z0-9]+)*'])
    ->name('magazines.article');
Route::get('/magazines/{issue_code}', [MagazineController::class, 'show'])
    ->where('issue_code', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('magazines.show');
// AI-GEN-END

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

    Route::resource('tags', TagController::class)->except(['show']);
    Route::get('/repos-snapshots', [ReposSnapshotController::class, 'index'])->name('repos-snapshots.index');
    Route::get('/repos-snapshots/{reposSnapshot}/tags', [ReposSnapshotController::class, 'editTags'])->name('repos-snapshots.edit-tags');
    Route::put('/repos-snapshots/{reposSnapshot}/tags', [ReposSnapshotController::class, 'updateTags'])->name('repos-snapshots.update-tags');

    Route::resource('magazine-issues', MagazineIssueController::class)->except(['show']);
    Route::resource('articles', ArticleController::class)->except(['show']);
});
// AI-GEN-END

require __DIR__.'/auth.php';
