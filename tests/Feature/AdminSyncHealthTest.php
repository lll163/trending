<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

/**
 * 管理员「同步失败摘要」只读页权限与展示。
 *
 * 依赖 sqlite 内存库与 pdo_sqlite；无扩展时 PHPUnit 会跳过本类（与 phpunit.xml 默认配置一致）。
 */
#[RequiresPhpExtension('pdo_sqlite')]
class AdminSyncHealthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理员可查看同步失败列表并看到错误文案。
     */
    public function test_admin_sees_sync_failures(): void
    {
        $admin = User::factory()->admin()->create();

        ReposSnapshot::query()->create([
            'github_owner' => 'bad',
            'github_repo' => 'repo',
            'description' => null,
            'stars_cnt' => 0,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => 'API rate limit',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.sync-health.index'))
            ->assertOk()
            ->assertSee('bad/repo', false)
            ->assertSee('API rate limit', false);
    }

    /**
     * 非管理员不可访问。
     */
    public function test_non_admin_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.sync-health.index'))
            ->assertForbidden();
    }

    /**
     * 未登录用户重定向到登录页。
     */
    public function test_guest_redirects_to_login(): void
    {
        $this->get(route('admin.sync-health.index'))
            ->assertRedirect(route('login'));
    }
}
// AI-GEN-END
