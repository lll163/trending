<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Models\RankingEntry;
use App\Models\ReposSnapshot;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * 首页聚合：榜单预览、最新通过投稿、入口链接。
 */
class HomeController extends Controller
{
    /**
     * 站点首页（Blade）。
     */
    public function index(): View
    {
        $rankingKey = (string) config('ranking.default_key', 'stars_public');

        $rankingSnapshots = $this->rankingPreviewSnapshots($rankingKey, 6);

        $latestApproved = Submission::query()
            ->with('user')
            ->where('status', SubmissionStatus::APPROVED)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('home', [
            'rankingKey' => $rankingKey,
            'rankingSnapshots' => $rankingSnapshots,
            'latestApprovedSubmissions' => $latestApproved,
        ]);
    }

    /**
     * 优先读物化榜单；若无数据则按 Star 数回退查询（首次部署尚未跑 rebuild 时）。
     *
     * @return Collection<int, ReposSnapshot>
     */
    private function rankingPreviewSnapshots(string $rankingKey, int $take): Collection
    {
        $ids = RankingEntry::query()
            ->where('ranking_key', $rankingKey)
            ->orderBy('position')
            ->limit($take)
            ->pluck('repos_snapshot_id');

        if ($ids->isEmpty()) {
            return ReposSnapshot::query()
                ->where('is_published', true)
                ->orderByDesc('stars_cnt')
                ->orderByDesc('updated_at')
                ->limit($take)
                ->get();
        }

        $snapshots = ReposSnapshot::query()->whereIn('id', $ids)->get()->keyBy('id');

        return $ids->map(fn ($id) => $snapshots->get((int) $id))->filter()->values();
    }
}
// AI-GEN-END
