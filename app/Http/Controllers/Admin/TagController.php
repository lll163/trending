<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * 管理员维护标签（slug + 标题）。
 */
class TagController extends Controller
{
    /**
     * 标签列表。
     */
    public function index(): View
    {
        $tags = Tag::query()->orderBy('title')->paginate(40);

        return view('admin.tags.index', ['tags' => $tags]);
    }

    /**
     * 新建表单。
     */
    public function create(): View
    {
        return view('admin.tags.create');
    }

    /**
     * 保存新标签。
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedTag($request);

        Tag::query()->create($data);

        return redirect()->route('admin.tags.index')->with('status', 'tag-created');
    }

    /**
     * 编辑表单。
     */
    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', ['tag' => $tag]);
    }

    /**
     * 更新标签。
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $this->validatedTag($request, $tag->id);

        $tag->update($data);

        return redirect()->route('admin.tags.index')->with('status', 'tag-updated');
    }

    /**
     * 删除标签（先解除与仓库的关联）。
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->reposSnapshots()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('status', 'tag-deleted');
    }

    /**
     * @return array{slug: string, title: string}
     */
    private function validatedTag(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = Rule::unique('tags', 'slug');
        if ($ignoreId !== null) {
            $slugRule = $slugRule->ignore($ignoreId);
        }

        /** @var array{slug: string, title: string} $data */
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:128', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'title' => ['required', 'string', 'max:255'],
        ]);

        return $data;
    }
}
// AI-GEN-END
