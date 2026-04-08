<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 项目分类标签。
 */
class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'slug',
        'title',
    ];

    /**
     * 打上该标签的仓库快照。
     */
    public function reposSnapshots(): BelongsToMany
    {
        return $this->belongsToMany(ReposSnapshot::class, 'repo_tag', 'tag_id', 'repos_snapshot_id')
            ->withTimestamps();
    }
}
// AI-GEN-END
