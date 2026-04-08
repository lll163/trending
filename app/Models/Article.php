<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 站内文章（可归属某月刊期号）。
 */
class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'magazine_issue_id',
        'slug',
        'title',
        'body',
        'sort_order',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * 所属月刊；可为空表示独立文章。
     */
    public function magazineIssue(): BelongsTo
    {
        return $this->belongsTo(MagazineIssue::class, 'magazine_issue_id');
    }
}
// AI-GEN-END
