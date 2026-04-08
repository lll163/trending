<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 平台收录仓库的 GitHub 元数据快照。
 */
class ReposSnapshot extends Model
{
    protected $table = 'repos_snapshots';

    protected $fillable = [
        'github_owner',
        'github_repo',
        'description',
        'stars_cnt',
        'forks_cnt',
        'default_branch',
        'homepage_url',
        'pushed_at',
        'snapshot_synced_at',
        'snapshot_sync_error',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'pushed_at' => 'datetime',
            'snapshot_synced_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    /**
     * 关联标签（经中间表 repo_tag）。
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'repo_tag', 'repos_snapshot_id', 'tag_id')
            ->withTimestamps();
    }

    /**
     * 关联的投稿记录（审核通过后可能指向本快照）。
     *
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'repos_snapshot_id');
    }
}
// AI-GEN-END
