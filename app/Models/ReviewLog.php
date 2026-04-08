<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 投稿人工审核操作记录。
 */
class ReviewLog extends Model
{
    protected $table = 'review_logs';

    protected $fillable = [
        'submission_id',
        'admin_user_id',
        'action',
        'remark',
    ];

    /**
     * 对应投稿。
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * 执行审核的管理员用户。
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
// AI-GEN-END
