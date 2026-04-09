<?php

namespace App\Services\Submission\Contracts;

// AI-GEN-BEGIN
/**
 * 黑名单匹配：返回首条命中的人类可读原因，未命中返回 null。
 */
interface BlacklistMatcherInterface
{
    /**
     * @param  string  $owner  小写
     * @param  string  $repo  小写
     */
    public function matchMessage(string $owner, string $repo, string $pitch): ?string;
}
// AI-GEN-END
