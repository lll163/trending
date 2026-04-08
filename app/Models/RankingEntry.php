<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 某榜单键下的一条排名结果（物化快照）。
 */
class RankingEntry extends Model
{
    protected $table = 'ranking_entries';

    protected $fillable = [
        'ranking_key',
        'repos_snapshot_id',
        'position',
        'score',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'computed_at' => 'datetime',
        ];
    }

    /**
     * 对应仓库快照。
     */
    public function reposSnapshot(): BelongsTo
    {
        return $this->belongsTo(ReposSnapshot::class, 'repos_snapshot_id');
    }
}
// AI-GEN-END
