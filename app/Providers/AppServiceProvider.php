<?php

namespace App\Providers;

// AI-GEN-BEGIN
use App\Services\GitHub\GitHubRepositoryClient;
use App\Services\GitHub\RepositorySnapshotGitHubSync;
use App\Services\Submission\AutoRulePipeline;
use App\Services\Submission\BlacklistMatcher;
use App\Services\Submission\Contracts\BlacklistMatcherInterface;
use App\Services\Submission\Contracts\DuplicateRepoCheckerInterface;
use App\Services\Submission\EloquentDuplicateRepoChecker;
// AI-GEN-END
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // AI-GEN-BEGIN
        $this->app->singleton(DuplicateRepoCheckerInterface::class, EloquentDuplicateRepoChecker::class);
        $this->app->singleton(BlacklistMatcherInterface::class, BlacklistMatcher::class);
        $this->app->singleton(AutoRulePipeline::class);

        $this->app->singleton(GitHubRepositoryClient::class, fn () => GitHubRepositoryClient::fromConfig());
        $this->app->singleton(RepositorySnapshotGitHubSync::class);
        // AI-GEN-END
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
