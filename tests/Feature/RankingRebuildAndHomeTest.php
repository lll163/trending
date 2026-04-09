<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\User;
use App\Services\Ranking\RankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 榜单重算与首页、榜单页可访问性。
 */
class RankingRebuildAndHomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * RankingService 按 Star 写入 ranking_entries 顺序。
     */
    public function test_ranking_service_orders_by_stars(): void
    {
        $low = ReposSnapshot::query()->create([
            'github_owner' => 'a',
            'github_repo' => 'low',
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

        $high = ReposSnapshot::query()->create([
            'github_owner' => 'b',
            'github_repo' => 'high',
            'description' => null,
            'stars_cnt' => 99,
            'forks_cnt' => 0,
            'default_branch' => null,
            'homepage_url' => null,
            'pushed_at' => null,
            'snapshot_synced_at' => null,
            'snapshot_sync_error' => null,
            'is_published' => true,
        ]);

        app(RankingService::class)->rebuild('test_stars');

        $this->assertDatabaseHas('ranking_entries', [
            'ranking_key' => 'test_stars',
            'position' => 1,
            'repos_snapshot_id' => $high->id,
        ]);

        $this->assertDatabaseHas('ranking_entries', [
            'ranking_key' => 'test_stars',
            'position' => 2,
            'repos_snapshot_id' => $low->id,
        ]);
    }

    /**
     * 首页与榜单页返回 200。
     */
    public function test_home_and_rankings_pages_ok(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('rankings.index'))->assertOk();
    }

    /**
     * 首页展示已通过投稿。
     */
    public function test_home_shows_approved_submission(): void
    {
        $user = User::factory()->create();
        Submission::query()->create([
            'user_id' => $user->id,
            'github_owner' => 'u',
            'github_repo' => 'v',
            'pitch' => 'ok',
            'status' => SubmissionStatus::APPROVED,
            'reject_reason' => null,
            'repos_snapshot_id' => null,
        ]);

        $this->get(route('home'))->assertOk()->assertSee('u/v', false);
    }
}
// AI-GEN-END
