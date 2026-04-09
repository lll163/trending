<?php

namespace App\Services\Submission;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Services\Submission\Contracts\DuplicateRepoCheckerInterface;

/**
 * 基于数据库的重复仓库检测（快照或进行中的投稿）。
 */
final class EloquentDuplicateRepoChecker implements DuplicateRepoCheckerInterface
{
    public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
    {
        if (ReposSnapshot::query()
            ->where('github_owner', $owner)
            ->where('github_repo', $repo)
            ->exists()) {
            return true;
        }

        $q = Submission::query()
            ->where('github_owner', $owner)
            ->where('github_repo', $repo)
            ->whereNotIn('status', SubmissionStatus::terminalRejected());

        if ($ignoreSubmissionId !== null) {
            $q->where('id', '!=', $ignoreSubmissionId);
        }

        return $q->exists();
    }
}
// AI-GEN-END
