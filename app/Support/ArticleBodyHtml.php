<?php

namespace App\Support;

// AI-GEN-BEGIN
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * 文章正文 Markdown → HTML（前台展示）。
 */
class ArticleBodyHtml
{
    /**
     * 将 Markdown 转为可放入 Blade 的 HTML；剥离未信任 HTML 输入以降低 XSS 风险。
     *
     * @param  string  $markdown  原始 Markdown
     */
    public static function fromMarkdown(string $markdown): HtmlString
    {
        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
        ]);

        return new HtmlString($html);
    }
}
// AI-GEN-END
