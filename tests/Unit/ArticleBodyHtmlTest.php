<?php

namespace Tests\Unit;

// AI-GEN-BEGIN
use App\Support\ArticleBodyHtml;
use Tests\TestCase;

/**
 * Markdown 渲染安全相关单测（依赖 Laravel Str::markdown）。
 */
class ArticleBodyHtmlTest extends TestCase
{
    /**
     * 原始 HTML 输入应被剥离，避免脚本注入。
     */
    public function test_raw_html_is_stripped_in_markdown_pipeline(): void
    {
        $html = (string) ArticleBodyHtml::fromMarkdown('<script>alert(1)</script>'."\n\n".'# Hello');

        $this->assertStringContainsString('Hello', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
}
// AI-GEN-END
