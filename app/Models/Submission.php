<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 用户提交的项目收录申请。
 */
class Submission extends Model
{
    protected $table = 'submissions';

    protected $fillable = [
        'user_id',
        'github_owner',
        'github_repo',
        'pitch',
        'status',
        'reject_reason',
        'repos_snapshot_id',
    ];

    /**
     * 投稿人。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 审核通过后关联的仓库快照。
     */
    public function reposSnapshot(): BelongsTo
    {
        return $this->belongsTo(ReposSnapshot::class, 'repos_snapshot_id');
    }

    /**
     * 审核操作历史。
     *
     * @return HasMany<ReviewLog, $this>
     */
    public function reviewLogs(): HasMany
    {
        return $this->hasMany(ReviewLog::class, 'submission_id');
    }
}
// AI-GEN-END
