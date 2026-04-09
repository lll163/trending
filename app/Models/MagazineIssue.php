<?php

namespace App\Models;

// AI-GEN-BEGIN
use Database\Factories\MagazineIssueFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 月刊期号（封面、目录等）。
 */
class MagazineIssue extends Model
{
    /** @use HasFactory<MagazineIssueFactory> */
    use HasFactory;

    protected $table = 'magazine_issues';

    /**
     * 前台路由使用期号 `issue_code` 解析模型。
     */
    public function getRouteKeyName(): string
    {
        return 'issue_code';
    }

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

    /**
     * 仅已发布期号（前台）。
     *
     * @param  Builder<MagazineIssue>  $query
     * @return Builder<MagazineIssue>
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
// AI-GEN-END
