<?php

namespace Tests;

// AI-GEN-BEGIN
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
// AI-GEN-END
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    // AI-GEN-BEGIN
    /**
     * PHPUnit 使用 sqlite :memory: 时，补充业务字段与 oauth_accounts 表，便于功能测试（与 database/sql 中 MySQL 脚本语义对齐，不执行 DROP）。
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite') {
            return;
        }

        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false);
            });
        }

        if (! Schema::hasColumn('users', 'github_bound_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('github_bound_at')->nullable();
            });
        }

        if (! Schema::hasTable('oauth_accounts')) {
            Schema::create('oauth_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('provider', 32);
                $table->string('provider_user_id', 64);
                $table->string('provider_login')->nullable();
                $table->text('access_token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->timestamps();
                $table->unique(['provider', 'provider_user_id'], 'uniq_oauth_provider_user');
            });
        }

        if (! Schema::hasTable('repos_snapshots')) {
            Schema::create('repos_snapshots', function (Blueprint $table) {
                $table->id();
                $table->string('github_owner', 255);
                $table->string('github_repo', 255);
                $table->text('description')->nullable();
                $table->unsignedInteger('stars_cnt')->default(0);
                $table->unsignedInteger('forks_cnt')->default(0);
                $table->string('default_branch')->nullable();
                $table->string('homepage_url', 512)->nullable();
                $table->timestamp('pushed_at')->nullable();
                $table->timestamp('snapshot_synced_at')->nullable();
                $table->string('snapshot_sync_error', 500)->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
                $table->unique(['github_owner', 'github_repo'], 'uniq_repos_snapshots_github_coords');
            });
        }

        // AI-GEN-BEGIN
        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 128);
                $table->string('title', 255);
                $table->timestamps();
                $table->unique('slug', 'uniq_tags_slug');
            });
        }

        if (! Schema::hasTable('repo_tag')) {
            Schema::create('repo_tag', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('repos_snapshot_id');
                $table->unsignedBigInteger('tag_id');
                $table->timestamps();
                $table->unique(['repos_snapshot_id', 'tag_id'], 'uniq_repo_tag_pair');
                $table->index('tag_id', 'idx_repo_tag_tag_id');
            });
        }
        // AI-GEN-END

        if (! Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('github_owner', 255);
                $table->string('github_repo', 255);
                $table->text('pitch')->nullable();
                $table->string('status', 32)->default('pending_auto');
                $table->string('reject_reason', 500)->nullable();
                $table->unsignedBigInteger('repos_snapshot_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('review_logs')) {
            Schema::create('review_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('submission_id');
                $table->unsignedBigInteger('admin_user_id');
                $table->string('action', 32);
                $table->string('remark', 500)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blacklist_entries')) {
            Schema::create('blacklist_entries', function (Blueprint $table) {
                $table->id();
                $table->string('pattern_type', 32);
                $table->string('pattern_value', 512);
                $table->timestamps();
            });
        }
    }
    // AI-GEN-END
}
