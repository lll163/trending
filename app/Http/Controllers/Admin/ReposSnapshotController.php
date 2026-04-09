<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\ReposSnapshot;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 管理员为仓库快照维护标签关联。
 */
class ReposSnapshotController extends Controller
{
    /**
     * 快照列表（含未发布，便于运营）。
     */
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $q = is_string($q) ? trim($q) : '';

        $query = ReposSnapshot::query()->with('tags')->orderByDesc('updated_at');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('github_owner', 'like', $like)
                    ->orWhere('github_repo', 'like', $like);
            });
        }

        $snapshots = $query->paginate(30)->withQueryString();

        return view('admin.repos_snapshots.index', [
            'snapshots' => $snapshots,
            'search' => $q,
        ]);
    }

    /**
     * 编辑某快照的标签多选。
     */
    public function editTags(ReposSnapshot $reposSnapshot): View
    {
        $tags = Tag::query()->orderBy('title')->get();
        $reposSnapshot->load('tags');

        return view('admin.repos_snapshots.edit-tags', [
            'reposSnapshot' => $reposSnapshot,
            'tags' => $tags,
        ]);
    }

    /**
     * 同步中间表 repo_tag。
     */
    public function updateTags(Request $request, ReposSnapshot $reposSnapshot): RedirectResponse
    {
        $validated = $request->validate([
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $ids = $validated['tag_ids'] ?? [];
        $reposSnapshot->tags()->sync($ids);

        return redirect()
            ->route('admin.repos-snapshots.index')
            ->with('status', 'repos-tags-updated');
    }
}
// AI-GEN-END
