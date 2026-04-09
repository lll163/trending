<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\Article;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 月刊前台列表、期详情、文章页；草稿不可见；管理端权限。
 */
class MagazinePublicFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 已发布期号与两篇已发布文章可在前台访问。
     */
    public function test_published_issue_and_two_articles_are_public(): void
    {
        $issue = MagazineIssue::factory()->published()->create([
            'issue_code' => '2026-test',
            'title' => '测试月刊特刊',
        ]);

        Article::factory()->published()->create([
            'magazine_issue_id' => $issue->id,
            'slug' => 'first-article',
            'title' => '第一篇公开',
            'sort_order' => 0,
            'body' => "## 小节\n\n正文 **粗体**",
        ]);

        Article::factory()->published()->create([
            'magazine_issue_id' => $issue->id,
            'slug' => 'second-article',
            'title' => '第二篇公开',
            'sort_order' => 1,
            'body' => 'Hello',
        ]);

        $this->get(route('magazines.index'))
            ->assertOk()
            ->assertSee('2026-test', false)
            ->assertSee('测试月刊特刊', false);

        $this->get(route('magazines.show', '2026-test'))
            ->assertOk()
            ->assertSee('第一篇公开', false)
            ->assertSee('第二篇公开', false);

        $this->get(route('magazines.article', [
            'issue_code' => '2026-test',
            'article_slug' => 'first-article',
        ]))
            ->assertOk()
            ->assertSee('第一篇公开', false)
            ->assertSee('粗体', false);
    }

    /**
     * 草稿期号不出现在月刊列表。
     */
    public function test_draft_issue_is_hidden_from_public_index(): void
    {
        MagazineIssue::factory()->create([
            'issue_code' => 'secret-draft',
            'title' => '不应出现在列表',
            'status' => 'draft',
        ]);

        $this->get(route('magazines.index'))
            ->assertOk()
            ->assertDontSee('secret-draft', false);
    }

    /**
     * 未登录用户访问月刊后台会被重定向到登录页。
     */
    public function test_guest_redirected_from_admin_magazine_issues(): void
    {
        $this->get(route('admin.magazine-issues.index'))->assertRedirect(route('login', absolute: false));
    }

    /**
     * 非管理员不能访问月刊后台。
     */
    public function test_non_admin_forbidden_on_admin_magazine_issues(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.magazine-issues.index'))->assertForbidden();
    }
}
// AI-GEN-END
