<?php

namespace App\Services\Submission\Contracts;

// AI-GEN-BEGIN
/**
 * 检测仓库坐标是否已被收录或存在未终结的重复投稿。
 */
interface DuplicateRepoCheckerInterface
{
    /**
     * @param  string  $owner  已小写规范化
     * @param  string  $repo  已小写规范化
     * @param  int|null  $ignoreSubmissionId  更新投稿时排除自身 id
     */
    public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool;
}
// AI-GEN-END
