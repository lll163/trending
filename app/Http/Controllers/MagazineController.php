<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Models\Article;
use App\Models\MagazineIssue;
use App\Support\ArticleBodyHtml;
use Illuminate\View\View;

/**
 * 前台月刊列表、期详情与文章阅读（仅已发布）。
 */
class MagazineController extends Controller
{
    /**
     * 已发布期号列表。
     */
    public function index(): View
    {
        $issues = MagazineIssue::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('magazines.index', ['issues' => $issues]);
    }

    /**
     * 指定期号下的目录（已发布文章）。
     */
    public function show(string $issue_code): View
    {
        $issue = MagazineIssue::query()
            ->published()
            ->where('issue_code', $issue_code)
            ->firstOrFail();

        $articles = $issue->articles()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('magazines.show', [
            'issue' => $issue,
            'articles' => $articles,
        ]);
    }

    /**
     * 阅读期内单篇文章（Markdown 渲染）。
     *
     * @param  string  $issue_code  期号 slug
     * @param  string  $article_slug  文章 slug
     */
    public function article(string $issue_code, string $article_slug): View
    {
        $issue = MagazineIssue::query()
            ->published()
            ->where('issue_code', $issue_code)
            ->firstOrFail();

        $article = Article::query()
            ->published()
            ->where('magazine_issue_id', $issue->id)
            ->where('slug', $article_slug)
            ->firstOrFail();

        $bodyHtml = ArticleBodyHtml::fromMarkdown($article->body);

        return view('magazines.article', [
            'issue' => $issue,
            'article' => $article,
            'bodyHtml' => $bodyHtml,
        ]);
    }
}
// AI-GEN-END
