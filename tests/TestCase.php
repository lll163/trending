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
    }
    // AI-GEN-END
}
