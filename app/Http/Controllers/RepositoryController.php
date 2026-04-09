<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Models\ReposSnapshot;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 前台开源仓库列表与详情（本地快照数据，按标签筛选）。
 */
class RepositoryController extends Controller
{
    /**
     * 已发布仓库分页列表，支持 query ?tag=slug。
     */
    public function index(Request $request): View
    {
        $tagSlug = $request->query('tag');
        $tagSlug = is_string($tagSlug) ? trim($tagSlug) : '';

        $query = ReposSnapshot::query()
            ->where('is_published', true)
            ->with('tags')
            ->orderByDesc('stars_cnt')
            ->orderByDesc('updated_at');

        if ($tagSlug !== '') {
            $query->whereHas('tags', function ($q) use ($tagSlug): void {
                $q->where('slug', $tagSlug);
            });
        }

        $snapshots = $query->paginate(20)->withQueryString();
        $tags = Tag::query()->orderBy('title')->get();

        return view('repositories.index', [
            'snapshots' => $snapshots,
            'tags' => $tags,
            'activeTag' => $tagSlug,
        ]);
    }

    /**
     * 按 owner/repo 展示单条已发布快照。
     */
    public function show(string $owner, string $repo): View
    {
        $owner = strtolower($owner);
        $repo = strtolower($repo);

        $snapshot = ReposSnapshot::query()
            ->where('is_published', true)
            ->where('github_owner', $owner)
            ->where('github_repo', $repo)
            ->with('tags')
            ->firstOrFail();

        return view('repositories.show', ['snapshot' => $snapshot]);
    }
}
// AI-GEN-END
