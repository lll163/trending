<?php

namespace App\Services\Submission;

// AI-GEN-BEGIN
/**
 * 投稿草稿值对象（已规范化 owner/repo 小写）。
 */
final class SubmissionDraft
{
    public readonly string $githubOwner;

    public readonly string $githubRepo;

    public readonly string $pitch;

    public function __construct(
        string $githubOwner,
        string $githubRepo,
        string $pitch,
    ) {
        $this->githubOwner = strtolower(trim($githubOwner));
        $this->githubRepo = strtolower(trim($githubRepo));
        $this->pitch = trim($pitch);
    }

    /**
     * @param  array{github_owner:string,github_repo:string,pitch:string}  $input
     */
    public static function fromValidated(array $input): self
    {
        return new self(
            $input['github_owner'],
            $input['github_repo'],
            $input['pitch'],
        );
    }
}
// AI-GEN-END
