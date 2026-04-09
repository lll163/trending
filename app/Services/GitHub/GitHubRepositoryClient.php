<?php

namespace App\Services\GitHub;

// AI-GEN-BEGIN
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * 封装 GitHub REST GET /repos/{owner}/{repo}，映射到 `repos_snapshots` 可更新字段。
 */
final class GitHubRepositoryClient
{
    public function __construct(
        private readonly ?string $token,
    ) {}

    /**
     * 使用配置中的 `services.github.token` 构造客户端。
     */
    public static function fromConfig(): self
    {
        $raw = config('services.github.token');

        return new self(is_string($raw) ? $raw : null);
    }

    /**
     * 拉取仓库元数据。
     *
     * @param  string  $owner  GitHub owner（大小写不敏感，请求前会转小写）
     * @param  string  $repo  仓库名
     */
    public function fetch(string $owner, string $repo): GitHubRepositoryFetchResult
    {
        $token = trim((string) $this->token);
        if ($token === '') {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::Skipped,
                null,
                'GITHUB_TOKEN 未配置，跳过同步',
            );
        }

        $owner = strtolower(trim($owner));
        $repo = strtolower(trim($repo));

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
                ->withToken($token)
                ->timeout(30)
                ->get("https://api.github.com/repos/{$owner}/{$repo}");
        } catch (Throwable $e) {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::Failed,
                null,
                '请求异常: '.$e->getMessage(),
            );
        }

        if ($response->status() === 404) {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::NotFound,
                null,
                '仓库不存在或已删除 (404)',
            );
        }

        if ($response->status() === 403) {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::Forbidden,
                null,
                $this->shortenErrorMessage($response->json('message') ?? '禁止访问 (403)'),
            );
        }

        if (! $response->successful()) {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::Failed,
                null,
                'HTTP '.$response->status().': '.$this->shortenErrorMessage($response->body()),
            );
        }

        /** @var array<string, mixed>|null $json */
        $json = $response->json();
        if (! is_array($json)) {
            return new GitHubRepositoryFetchResult(
                GitHubRepositoryFetchStatus::Failed,
                null,
                '响应不是合法 JSON',
            );
        }

        $homepage = $json['homepage'] ?? null;
        $homepageUrl = is_string($homepage) && $homepage !== '' ? $homepage : null;

        $defaultBranch = $json['default_branch'] ?? null;
        $defaultBranch = is_string($defaultBranch) && $defaultBranch !== '' ? $defaultBranch : null;

        $description = $json['description'] ?? null;
        $description = is_string($description) ? $description : null;

        $pushedAt = null;
        if (! empty($json['pushed_at']) && is_string($json['pushed_at'])) {
            try {
                $pushedAt = Carbon::parse($json['pushed_at']);
            } catch (Throwable) {
                $pushedAt = null;
            }
        }

        $payload = [
            'description' => $description,
            'stars_cnt' => (int) ($json['stargazers_count'] ?? 0),
            'forks_cnt' => (int) ($json['forks_count'] ?? 0),
            'default_branch' => $defaultBranch,
            'homepage_url' => $homepageUrl,
            'pushed_at' => $pushedAt,
        ];

        return new GitHubRepositoryFetchResult(GitHubRepositoryFetchStatus::Success, $payload, null);
    }

    /**
     * 截断过长错误信息，适配 `snapshot_sync_error` 字段长度。
     */
    private function shortenErrorMessage(string $message): string
    {
        $message = trim($message);
        if (strlen($message) <= 480) {
            return $message;
        }

        return substr($message, 0, 477).'...';
    }
}
// AI-GEN-END
