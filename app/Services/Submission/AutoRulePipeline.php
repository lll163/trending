<?php

namespace App\Services\Submission;

// AI-GEN-BEGIN
use App\Services\Submission\Contracts\BlacklistMatcherInterface;
use App\Services\Submission\Contracts\DuplicateRepoCheckerInterface;

/**
 * 投稿自动规则：必填、坐标格式、黑名单、配置关键词、去重。
 */
final class AutoRulePipeline
{
    /**
     * @param  list<string>|null  $blockedKeywordsOverride  非 null 时用于测试覆盖 config（生产保持 null）
     */
    public function __construct(
        private DuplicateRepoCheckerInterface $duplicateChecker,
        private BlacklistMatcherInterface $blacklistMatcher,
        private ?array $blockedKeywordsOverride = null,
    ) {}

    /**
     * @param  int|null  $ignoreSubmissionId  更新已有投稿时传入以排除自身
     */
    public function evaluate(SubmissionDraft $draft, ?int $ignoreSubmissionId = null): AutoRuleResult
    {
        $errors = [];

        if ($draft->githubOwner === '' || $draft->githubRepo === '') {
            $errors[] = '请填写完整的 GitHub 仓库坐标。';
        }

        if ($draft->pitch === '') {
            $errors[] = '请填写项目推荐语。';
        }

        if ($errors !== []) {
            return AutoRuleResult::fail($errors);
        }

        if (! $this->validGithubOwnerOrOrg($draft->githubOwner)) {
            return AutoRuleResult::fail(['GitHub 用户名或组织名格式无效。']);
        }

        if (! $this->validRepoName($draft->githubRepo)) {
            return AutoRuleResult::fail(['仓库名称格式无效。']);
        }

        $blacklist = $this->blacklistMatcher->matchMessage(
            $draft->githubOwner,
            $draft->githubRepo,
            $draft->pitch
        );

        if ($blacklist !== null) {
            return AutoRuleResult::fail([$blacklist]);
        }

        foreach ($this->blockedKeywordsFromConfig() as $keyword) {
            if ($keyword !== '' && str_contains(mb_strtolower($draft->pitch), mb_strtolower($keyword))) {
                return AutoRuleResult::fail(['推荐语包含不允许的关键词。']);
            }
        }

        if ($this->duplicateChecker->isDuplicate($draft->githubOwner, $draft->githubRepo, $ignoreSubmissionId)) {
            return AutoRuleResult::fail(['该仓库已被收录或已有进行中的投稿。']);
        }

        return AutoRuleResult::ok();
    }

    private function validGithubOwnerOrOrg(string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}$/', $slug);
    }

    private function validRepoName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9._-]{1,100}$/', $name);
    }

    /**
     * @return list<string>
     */
    private function blockedKeywordsFromConfig(): array
    {
        if ($this->blockedKeywordsOverride !== null) {
            return array_values(array_filter(array_map(
                static fn (mixed $k): string => trim((string) $k),
                $this->blockedKeywordsOverride
            )));
        }

        $list = config('submission.blocked_keywords', []);

        if (! is_array($list)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $k): string => trim((string) $k),
            $list
        )));
    }
}
// AI-GEN-END
