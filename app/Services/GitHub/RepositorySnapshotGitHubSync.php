<?php

namespace App\Services\GitHub;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use Illuminate\Support\Facades\Log;

/**
 * 将 GitHub 客户端结果写入单条 `repos_snapshots`（供 Job 调用）。
 */
final class RepositorySnapshotGitHubSync
{
    public function __construct(
        private readonly GitHubRepositoryClient $client,
    ) {}

    /**
     * 同步一条快照：成功则更新元数据并清空错误；失败则只写 `snapshot_sync_error`；跳过则打日志不写库。
     *
     * @param  ReposSnapshot  $snapshot  已持久化的快照模型
     */
    public function sync(ReposSnapshot $snapshot): void
    {
        $result = $this->client->fetch($snapshot->github_owner, $snapshot->github_repo);

        if ($result->status === GitHubRepositoryFetchStatus::Skipped) {
            Log::info('github.snapshot_sync_skipped', [
                'component' => 'repository_snapshot_github_sync',
                'repos_snapshot_id' => $snapshot->id,
                'github_owner' => $snapshot->github_owner,
                'github_repo' => $snapshot->github_repo,
                'reason' => $result->message,
            ]);

            return;
        }

        if ($result->status === GitHubRepositoryFetchStatus::Success && is_array($result->snapshotPayload)) {
            $snapshot->update(array_merge($result->snapshotPayload, [
                'snapshot_synced_at' => now(),
                'snapshot_sync_error' => null,
            ]));

            return;
        }

        $message = $result->message ?? $result->status->value;
        $snapshot->update([
            'snapshot_sync_error' => $message,
        ]);

        Log::warning('github.snapshot_sync_failed', [
            'component' => 'repository_snapshot_github_sync',
            'repos_snapshot_id' => $snapshot->id,
            'github_owner' => $snapshot->github_owner,
            'github_repo' => $snapshot->github_repo,
            'fetch_status' => $result->status->value,
            'message' => $message,
        ]);
    }
}
// AI-GEN-END
