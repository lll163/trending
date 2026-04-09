<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\MagazineIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * 管理员维护月刊期号（封面存 `storage/app/public` 相对路径）。
 */
class MagazineIssueController extends Controller
{
    /**
     * 期号列表。
     */
    public function index(): View
    {
        $issues = MagazineIssue::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.magazine_issues.index', ['issues' => $issues]);
    }

    /**
     * 新建表单。
     */
    public function create(): View
    {
        return view('admin.magazine_issues.create');
    }

    /**
     * 保存期号；可选上传封面。
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedIssue($request);
        $this->applyPublishedDefaults($data);
        $this->storeCoverIfPresent($request, $data);

        MagazineIssue::query()->create($data);

        return redirect()->route('admin.magazine-issues.index')->with('status', 'magazine-issue-created');
    }

    /**
     * 编辑表单。
     */
    public function edit(MagazineIssue $magazine_issue): View
    {
        return view('admin.magazine_issues.edit', ['issue' => $magazine_issue]);
    }

    /**
     * 更新期号。
     */
    public function update(Request $request, MagazineIssue $magazine_issue): RedirectResponse
    {
        $data = $this->validatedIssue($request, $magazine_issue);
        $this->applyPublishedDefaults($data);
        $this->replaceCoverIfPresent($request, $magazine_issue, $data);

        $magazine_issue->update($data);

        return redirect()->route('admin.magazine-issues.index')->with('status', 'magazine-issue-updated');
    }

    /**
     * 删除期号（期内仍有文章时禁止）。
     */
    public function destroy(MagazineIssue $magazine_issue): RedirectResponse
    {
        if ($magazine_issue->articles()->exists()) {
            return redirect()
                ->route('admin.magazine-issues.index')
                ->with('status', 'magazine-issue-delete-blocked');
        }

        if ($magazine_issue->cover_path) {
            Storage::disk('public')->delete($magazine_issue->cover_path);
        }

        $magazine_issue->delete();

        return redirect()->route('admin.magazine-issues.index')->with('status', 'magazine-issue-deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedIssue(Request $request, ?MagazineIssue $ignore = null): array
    {
        $codeRule = Rule::unique('magazine_issues', 'issue_code');
        if ($ignore !== null) {
            $codeRule = $codeRule->ignore($ignore->id);
        }

        /** @var array{issue_code: string, title: string, status: string, published_at?: string|null} $data */
        $data = $request->validate([
            'issue_code' => ['required', 'string', 'max:32', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $codeRule],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['cover']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function applyPublishedDefaults(array &$data): void
    {
        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storeCoverIfPresent(Request $request, array &$data): void
    {
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('magazine-covers', 'public');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function replaceCoverIfPresent(Request $request, MagazineIssue $issue, array &$data): void
    {
        if (! $request->hasFile('cover')) {
            return;
        }

        if ($issue->cover_path) {
            Storage::disk('public')->delete($issue->cover_path);
        }

        $data['cover_path'] = $request->file('cover')->store('magazine-covers', 'public');
    }
}
// AI-GEN-END
