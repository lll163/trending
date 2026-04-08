<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 月刊期号（封面、目录等）。
 */
class MagazineIssue extends Model
{
    protected $table = 'magazine_issues';

    protected $fillable = [
        'issue_code',
        'title',
        'cover_path',
        'catalog_json',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'catalog_json' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * 期内文章列表。
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'magazine_issue_id');
    }
}
// AI-GEN-END
