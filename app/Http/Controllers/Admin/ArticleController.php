<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MagazineIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * 管理员维护文章（Markdown 正文、可选归属期号）。
 */
class ArticleController extends Controller
{
    /**
     * 文章列表（可按期号筛选）。
     */
    public function index(Request $request): View
    {
        $issueId = $request->query('magazine_issue_id');
        $issueId = is_numeric($issueId) ? (int) $issueId : null;

        $query = Article::query()->with('magazineIssue')->orderByDesc('updated_at');

        if ($issueId !== null) {
            $query->where('magazine_issue_id', $issueId);
        }

        $articles = $query->paginate(25)->withQueryString();
        $issues = MagazineIssue::query()->orderByDesc('published_at')->orderByDesc('id')->get();

        return view('admin.articles.index', [
            'articles' => $articles,
            'issues' => $issues,
            'filterIssueId' => $issueId,
        ]);
    }

    /**
     * 新建表单。
     */
    public function create(): View
    {
        $issues = MagazineIssue::query()->orderByDesc('published_at')->orderByDesc('id')->get();

        return view('admin.articles.create', ['issues' => $issues]);
    }

    /**
     * 保存文章。
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedArticle($request);
        $this->applyPublishedDefaults($data);

        Article::query()->create($data);

        return redirect()->route('admin.articles.index')->with('status', 'article-created');
    }

    /**
     * 编辑表单。
     */
    public function edit(Article $article): View
    {
        $issues = MagazineIssue::query()->orderByDesc('published_at')->orderByDesc('id')->get();

        return view('admin.articles.edit', [
            'article' => $article,
            'issues' => $issues,
        ]);
    }

    /**
     * 更新文章。
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validatedArticle($request, $article);
        $this->applyPublishedDefaults($data);

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('status', 'article-updated');
    }

    /**
     * 删除文章。
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'article-deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedArticle(Request $request, ?Article $ignore = null): array
    {
        $slugRule = Rule::unique('articles', 'slug');
        if ($ignore !== null) {
            $slugRule = $slugRule->ignore($ignore->id);
        }

        /** @var array{magazine_issue_id?: int|null, slug: string, title: string, body: string, sort_order: int, status: string, published_at?: string|null} $data */
        $data = $request->validate([
            'magazine_issue_id' => ['nullable', 'integer', 'exists:magazine_issues,id'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'status' => ['required', 'string', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

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
}
// AI-GEN-END
