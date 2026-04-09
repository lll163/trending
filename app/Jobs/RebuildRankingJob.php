<?php

namespace App\Jobs;

// AI-GEN-BEGIN
use App\Services\Ranking\RankingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 重算物化榜单（默认键见 config/ranking.php）。
 */
class RebuildRankingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  string|null  $rankingKey  为 null 时使用配置默认键
     */
    public function __construct(public ?string $rankingKey = null) {}

    public function handle(RankingService $rankingService): void
    {
        $key = $this->rankingKey ?? (string) config('ranking.default_key', 'stars_public');
        $rankingService->rebuild($key);
    }
}
// AI-GEN-END
