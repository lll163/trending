<?php

namespace App\Models;

// AI-GEN-BEGIN
/**
 * 投稿状态常量（与 database/sql 中 submissions.status 注释一致）。
 */
final class SubmissionStatus
{
    public const PENDING_AUTO = 'pending_auto';

    public const REJECTED_AUTO = 'rejected_auto';

    public const PENDING_REVIEW = 'pending_review';

    public const APPROVED = 'approved';

    public const REJECTED_REVIEW = 'rejected_review';

    /**
     * @return list<string>
     */
    public static function terminalRejected(): array
    {
        return [self::REJECTED_AUTO, self::REJECTED_REVIEW];
    }
}
// AI-GEN-END
