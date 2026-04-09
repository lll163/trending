<?php

namespace App\Services\Ranking;

// AI-GEN-BEGIN
use App\Models\RankingConfig;
use App\Models\RankingEntry;
use App\Models\ReposSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 根据配置将已发布仓库排序结果物化到 `ranking_entries`。
 */
final class RankingService
{
    /**
     * 重算指定榜单键：按 Star 降序，其次 `updated_at` 降序；可选按「最近更新天数」过滤。
     *
     * @param  string  $rankingKey  与表 ranking_key 一致
     */
    public function rebuild(string $rankingKey): void
    {
        $config = RankingConfig::query()->firstOrCreate(
            ['ranking_key' => $rankingKey],
            ['params_json' => config('ranking.default_params', [])]
        );

        $params = array_merge(
            config('ranking.default_params', []),
            is_array($config->params_json) ? $config->params_json : []
        );

        $limit = max(1, min(500, (int) ($params['limit'] ?? 30)));
        $minDaysRaw = $params['min_updated_days'] ?? null;
        $minDays = is_numeric($minDaysRaw) ? max(0, (int) $minDaysRaw) : null;

        $query = ReposSnapshot::query()
            ->where('is_published', true)
            ->orderByDesc('stars_cnt')
            ->orderByDesc('updated_at');

        // 仅当为正整数时过滤「最近 N 天内仍有更新」的快照；0 或 null 表示不限制
        if ($minDays !== null && $minDays > 0) {
            $threshold = Carbon::now()->subDays($minDays);
            $query->where('updated_at', '>=', $threshold);
        }

        $snapshots = $query->limit($limit)->get();

        $now = now();

        DB::transaction(function () use ($rankingKey, $snapshots, $now, $config): void {
            RankingEntry::query()->where('ranking_key', $rankingKey)->delete();

            $position = 1;
            foreach ($snapshots as $snapshot) {
                RankingEntry::query()->create([
                    'ranking_key' => $rankingKey,
                    'repos_snapshot_id' => $snapshot->id,
                    'position' => $position,
                    'score' => (string) $snapshot->stars_cnt,
                    'computed_at' => $now,
                ]);
                $position++;
            }

            $config->update(['last_computed_at' => $now]);
        });
    }
}
// AI-GEN-END
