<?php

namespace Tests\Unit;

// AI-GEN-BEGIN
use App\Services\Submission\AutoRulePipeline;
use App\Services\Submission\Contracts\BlacklistMatcherInterface;
use App\Services\Submission\Contracts\DuplicateRepoCheckerInterface;
use App\Services\Submission\SubmissionDraft;
use PHPUnit\Framework\TestCase;

/**
 * 自动规则流水线单元测试（依赖注入桩，不启动 Laravel、不访问数据库）。
 */
class SubmissionAutoRulesTest extends TestCase
{
    private function makePipeline(
        DuplicateRepoCheckerInterface $dup,
        BlacklistMatcherInterface $blacklist,
        ?array $keywordOverride = null,
    ): AutoRulePipeline {
        return new AutoRulePipeline($dup, $blacklist, $keywordOverride);
    }

    public function test_empty_pitch_fails(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return false;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return null;
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, []);
        $draft = new SubmissionDraft('laravel', 'framework', '');

        $result = $pipeline->evaluate($draft);

        $this->assertFalse($result->passed);
        $this->assertNotEmpty($result->messages);
    }

    public function test_invalid_owner_fails(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return false;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return null;
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, []);
        $draft = new SubmissionDraft('Bad_Owner', 'ok-repo', 'hello world');

        $result = $pipeline->evaluate($draft);

        $this->assertFalse($result->passed);
    }

    public function test_duplicate_fails(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return true;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return null;
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, []);
        $draft = new SubmissionDraft('laravel', 'framework', 'good pitch here');

        $result = $pipeline->evaluate($draft);

        $this->assertFalse($result->passed);
        $this->assertStringContainsString('收录', implode('', $result->messages));
    }

    public function test_blacklist_match_fails(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return false;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return '命中黑名单';
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, []);
        $draft = new SubmissionDraft('laravel', 'framework', 'pitch');

        $result = $pipeline->evaluate($draft);

        $this->assertFalse($result->passed);
        $this->assertContains('命中黑名单', $result->messages);
    }

    public function test_blocked_keyword_override_fails(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return false;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return null;
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, ['赌博']);
        $draft = new SubmissionDraft('laravel', 'framework', '这里有赌博内容');

        $result = $pipeline->evaluate($draft);

        $this->assertFalse($result->passed);
    }

    public function test_valid_draft_passes(): void
    {
        $dup = new class implements DuplicateRepoCheckerInterface
        {
            public function isDuplicate(string $owner, string $repo, ?int $ignoreSubmissionId = null): bool
            {
                return false;
            }
        };
        $bl = new class implements BlacklistMatcherInterface
        {
            public function matchMessage(string $owner, string $repo, string $pitch): ?string
            {
                return null;
            }
        };

        $pipeline = $this->makePipeline($dup, $bl, []);
        $draft = new SubmissionDraft('laravel', 'framework', '优秀的 PHP 框架。');

        $result = $pipeline->evaluate($draft);

        $this->assertTrue($result->passed);
    }
}
// AI-GEN-END
