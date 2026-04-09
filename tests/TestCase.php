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

        // 未启用 pdo_sqlite 时不访问数据库，避免 setUp 阶段即失败（部分 Unit 测试无需库）。
        if (! extension_loaded('pdo_sqlite')) {
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

        // AI-GEN-BEGIN
        if (! Schema::hasTable('magazine_issues')) {
            Schema::create('magazine_issues', function (Blueprint $table) {
                $table->id();
                $table->string('issue_code', 32);
                $table->string('title', 255);
                $table->string('cover_path', 512)->nullable();
                $table->json('catalog_json')->nullable();
                $table->string('status', 32)->default('draft');
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
                $table->unique('issue_code', 'uniq_magazine_issues_issue_code');
            });
        }

        if (! Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('magazine_issue_id')->nullable();
                $table->string('slug', 255);
                $table->string('title', 255);
                $table->longText('body');
                $table->integer('sort_order')->default(0);
                $table->string('status', 32)->default('draft');
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
                $table->unique('slug', 'uniq_articles_slug');
                $table->index('magazine_issue_id', 'idx_articles_magazine_issue_id');
            });
        }
        // AI-GEN-END
    }
    // AI-GEN-END
}
