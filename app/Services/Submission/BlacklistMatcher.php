<?php

namespace App\Services\Submission;

// AI-GEN-BEGIN
use App\Models\BlacklistEntry;
use App\Services\Submission\Contracts\BlacklistMatcherInterface;

/**
 * 从 blacklist_entries 表加载规则并匹配。
 */
final class BlacklistMatcher implements BlacklistMatcherInterface
{
    public function matchMessage(string $owner, string $repo, string $pitch): ?string
    {
        $full = $owner.'/'.$repo;
        $pitchLower = mb_strtolower($pitch);

        /** @var iterable<BlacklistEntry> $entries */
        $entries = BlacklistEntry::query()->get();

        foreach ($entries as $entry) {
            $value = trim($entry->pattern_value);
            if ($value === '') {
                continue;
            }

            if ($entry->pattern_type === 'owner_repo' && strtolower($value) === $full) {
                return '该仓库坐标在黑名单中。';
            }

            if ($entry->pattern_type === 'owner' && strtolower($value) === $owner) {
                return '该 GitHub 用户或组织在黑名单中。';
            }

            if ($entry->pattern_type === 'keyword' && str_contains($pitchLower, mb_strtolower($value))) {
                return '推荐语命中黑名单关键词。';
            }
        }

        return null;
    }
}
// AI-GEN-END
