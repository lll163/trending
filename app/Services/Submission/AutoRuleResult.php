<?php

namespace App\Services\Submission;

// AI-GEN-BEGIN
/**
 * 自动规则流水线执行结果。
 *
 * @param  list<string>  $messages
 */
final class AutoRuleResult
{
    /**
     * @param  list<string>  $messages
     */
    public function __construct(
        public readonly bool $passed,
        public readonly array $messages = [],
    ) {}

    public static function ok(): self
    {
        return new self(true, []);
    }

    /**
     * @param  list<string>  $messages
     */
    public static function fail(array $messages): self
    {
        return new self(false, $messages);
    }

    public function summaryMessage(): string
    {
        return implode('；', $this->messages);
    }
}
// AI-GEN-END
