<?php

namespace App\Jobs;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Services\GitHub\RepositorySnapshotGitHubSync;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 同步单条 `repos_snapshots` 的 GitHub 元数据（队列任务，可单独重试）。
 */
class SyncRepositorySnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  int  $reposSnapshotId  快照主键
     */
    public function __construct(public int $reposSnapshotId) {}

    public function handle(RepositorySnapshotGitHubSync $sync): void
    {
        $snapshot = ReposSnapshot::query()->find($this->reposSnapshotId);
        if ($snapshot === null) {
            return;
        }

        try {
            $sync->sync($snapshot);
        } catch (Throwable $e) {
            Log::error('github.snapshot_sync_exception', [
                'repos_snapshot_id' => $this->reposSnapshotId,
                'message' => $e->getMessage(),
            ]);

            $snapshot->update([
                'snapshot_sync_error' => '异常: '.mb_substr($e->getMessage(), 0, 450),
            ]);
        }
    }
}
// AI-GEN-END
