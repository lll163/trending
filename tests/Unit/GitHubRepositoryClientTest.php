<?php

namespace Tests\Unit;

// AI-GEN-BEGIN
use App\Services\GitHub\GitHubRepositoryClient;
use App\Services\GitHub\GitHubRepositoryFetchStatus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * GitHub 仓库元数据客户端（Http::fake 覆盖 200/404/403 与空 Token）。
 */
class GitHubRepositoryClientTest extends TestCase
{
    /**
     * Token 为空时不发 HTTP，返回 Skipped。
     */
    public function test_fetch_skips_when_token_empty(): void
    {
        Http::fake();

        $client = new GitHubRepositoryClient('');
        $result = $client->fetch('octocat', 'hello-world');

        $this->assertSame(GitHubRepositoryFetchStatus::Skipped, $result->status);
        $this->assertNotNull($result->message);
        Http::assertNothingSent();
    }

    /**
     * 200 响应映射为快照字段。
     */
    public function test_fetch_maps_success_payload(): void
    {
        Http::fake([
            'https://api.github.com/repos/octocat/hello-world' => Http::response([
                'description' => 'Test repo',
                'stargazers_count' => 42,
                'forks_count' => 7,
                'default_branch' => 'main',
                'homepage' => 'https://example.com',
                'pushed_at' => '2020-01-01T00:00:00Z',
            ], 200),
        ]);

        $client = new GitHubRepositoryClient('fake-token');
        $result = $client->fetch('Octocat', 'Hello-World');

        $this->assertSame(GitHubRepositoryFetchStatus::Success, $result->status);
        $this->assertIsArray($result->snapshotPayload);
        $this->assertSame('Test repo', $result->snapshotPayload['description']);
        $this->assertSame(42, $result->snapshotPayload['stars_cnt']);
        $this->assertSame(7, $result->snapshotPayload['forks_cnt']);
        $this->assertSame('main', $result->snapshotPayload['default_branch']);
        $this->assertSame('https://example.com', $result->snapshotPayload['homepage_url']);
        $this->assertNotNull($result->snapshotPayload['pushed_at']);
    }

    /**
     * 404 返回 NotFound。
     */
    public function test_fetch_not_found_on_404(): void
    {
        Http::fake([
            'https://api.github.com/repos/o/r' => Http::response(['message' => 'Not Found'], 404),
        ]);

        $client = new GitHubRepositoryClient('fake-token');
        $result = $client->fetch('o', 'r');

        $this->assertSame(GitHubRepositoryFetchStatus::NotFound, $result->status);
    }

    /**
     * 403 返回 Forbidden。
     */
    public function test_fetch_forbidden_on_403(): void
    {
        Http::fake([
            'https://api.github.com/repos/o/r' => Http::response(['message' => 'API rate limit exceeded'], 403),
        ]);

        $client = new GitHubRepositoryClient('fake-token');
        $result = $client->fetch('o', 'r');

        $this->assertSame(GitHubRepositoryFetchStatus::Forbidden, $result->status);
        $this->assertStringContainsString('rate limit', (string) $result->message);
    }
}
// AI-GEN-END
