<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Models\RankingEntry;
use App\Models\ReposSnapshot;
use Illuminate\View\View;

/**
 * 前台榜单列表（读 `ranking_entries`；空时回退实时排序）。
 */
class RankingController extends Controller
{
    /**
     * 榜单全表：默认键或指定键。
     *
     * @param  string|null  $ranking_key  URL 段，缺省用配置默认键
     */
    public function index(?string $ranking_key = null): View
    {
        $key = $ranking_key ?? (string) config('ranking.default_key', 'stars_public');

        $entries = RankingEntry::query()
            ->where('ranking_key', $key)
            ->with('reposSnapshot')
            ->orderBy('position')
            ->paginate(20);

        if ($entries->isEmpty()) {
            $snapshots = ReposSnapshot::query()
                ->where('is_published', true)
                ->orderByDesc('stars_cnt')
                ->orderByDesc('updated_at')
                ->paginate(20);

            return view('rankings.index', [
                'rankingKey' => $key,
                'entries' => $entries,
                'fallbackSnapshots' => $snapshots,
            ]);
        }

        return view('rankings.index', [
            'rankingKey' => $key,
            'entries' => $entries,
            'fallbackSnapshots' => null,
        ]);
    }
}
// AI-GEN-END
