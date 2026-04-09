<?php

namespace App\Jobs;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 对全部「已发布」仓库快照依次派发单条同步任务（单条失败不影响后续）。
 */
class SyncAllRepositoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        ReposSnapshot::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->lazyById(100)
            ->each(function (ReposSnapshot $snapshot): void {
                SyncRepositorySnapshotJob::dispatchSync($snapshot->id);
            });
    }
}
// AI-GEN-END
