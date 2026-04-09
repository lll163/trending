<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 前台仓库列表/详情与管理员标签、快照打标流程。
 */
class RepositoryAndTagAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 仅展示已发布快照；未发布不出现在列表。
     */
    public function test_repository_index_lists_only_published(): void
    {
        ReposSnapshot::query()->create([
            'github_owner' => 'pub-owner',
            'github_repo' => 'pub-repo',
            'description' => 'visible',
            'stars_cnt' => 10,
            'forks_cnt' => 0,
            'default_branch' => 'main',
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);

        ReposSnapshot::query()->create([
            'github_owner' => 'hidden-owner',
            'github_repo' => 'hidden-repo',
            'description' => 'secret',
            'stars_cnt' => 1,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => false,
        ]);

        $response = $this->get(route('repositories.index'));

        $response->assertOk();
        $response->assertSee('pub-owner/pub-repo', false);
        $response->assertDontSee('hidden-owner/hidden-repo', false);
    }

    /**
     * ?tag=slug 仅返回带该标签的已发布快照。
     */
    public function test_repository_index_filters_by_tag_slug(): void
    {
        $tagPhp = Tag::factory()->create(['slug' => 'php-lang', 'title' => 'PHP']);
        $tagGo = Tag::factory()->create(['slug' => 'go-lang', 'title' => 'Go']);

        $snapPhp = ReposSnapshot::query()->create([
            'github_owner' => 'php-owner',
            'github_repo' => 'php-repo',
            'description' => null,
            'stars_cnt' => 5,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);
        $snapPhp->tags()->attach($tagPhp->id);

        $snapGo = ReposSnapshot::query()->create([
            'github_owner' => 'go-owner',
            'github_repo' => 'go-repo',
            'description' => null,
            'stars_cnt' => 3,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);
        $snapGo->tags()->attach($tagGo->id);

        $this->get(route('repositories.index', ['tag' => 'php-lang']))
            ->assertOk()
            ->assertSee('php-owner/php-repo', false)
            ->assertDontSee('go-owner/go-repo', false);
    }

    /**
     * 已发布快照详情页可访问（owner/repo 不区分大小写）。
     */
    public function test_repository_show_resolves_case_insensitive(): void
    {
        ReposSnapshot::query()->create([
            'github_owner' => 'mixedcase',
            'github_repo' => 'demo',
            'description' => 'hello',
            'stars_cnt' => 2,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);

        $this->get('/repositories/MixedCase/demo')->assertOk()->assertSee('hello', false);
    }

    /**
     * 管理员可同步快照与标签的中间表。
     */
    public function test_admin_can_update_snapshot_tags(): void
    {
        $admin = User::factory()->admin()->create();
        $snap = ReposSnapshot::query()->create([
            'github_owner' => 'acme',
            'github_repo' => 'widget',
            'description' => null,
            'stars_cnt' => 0,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);
        $t1 = Tag::factory()->create();
        $t2 = Tag::factory()->create();

        $response = $this->actingAs($admin)->put(
            route('admin.repos-snapshots.update-tags', $snap),
            ['tag_ids' => [$t1->id, $t2->id]]
        );

        $response->assertRedirect(route('admin.repos-snapshots.index'));
        $snap->refresh();
        $this->assertCount(2, $snap->tags);
    }

    /**
     * 非管理员不能更新快照标签。
     */
    public function test_non_admin_cannot_update_snapshot_tags(): void
    {
        $user = User::factory()->withGithubBound()->create(['is_admin' => false]);
        $snap = ReposSnapshot::query()->create([
            'github_owner' => 'x',
            'github_repo' => 'y',
            'description' => null,
            'stars_cnt' => 0,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);
        $tag = Tag::factory()->create();

        $this->actingAs($user)->put(
            route('admin.repos-snapshots.update-tags', $snap),
            ['tag_ids' => [$tag->id]]
        )->assertForbidden();
    }
}
// AI-GEN-END
