<?php

namespace App\Services\GitHub;

// AI-GEN-BEGIN
/**
 * GitHub 仓库元数据拉取结果（不抛异常，由调用方分支处理）。
 */
final readonly class GitHubRepositoryFetchResult
{
    /**
     * @param  array<string, mixed>|null  $snapshotPayload  仅当 status 为 Success 时有值（与 repos_snapshots 可更新字段对应）
     */
    public function __construct(
        public GitHubRepositoryFetchStatus $status,
        public ?array $snapshotPayload = null,
        public ?string $message = null,
    ) {}
}
// AI-GEN-END
