<?php

namespace App\Policies;

// AI-GEN-BEGIN
use App\Models\Submission;
use App\Models\User;

/**
 * 投稿权限：创建/更新需已绑定 GitHub；仅本人可改自己的投稿。
 */
class SubmissionPolicy
{
    /**
     * 是否允许创建投稿（不要求已存在模型实例）。
     */
    public function create(User $user): bool
    {
        return $user->github_bound_at !== null;
    }

    /**
     * 是否允许更新指定投稿。
     */
    public function update(User $user, Submission $submission): bool
    {
        return $user->github_bound_at !== null && (int) $user->id === (int) $submission->user_id;
    }
}
// AI-GEN-END
