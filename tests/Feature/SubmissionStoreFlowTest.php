<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\SubmissionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionStoreFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 已绑定 GitHub 的用户提交合法仓库后进入待审核。
     */
    public function test_user_submission_passes_auto_rules_and_is_pending_review(): void
    {
        $user = User::factory()->withGithubBound()->create();

        $response = $this->actingAs($user)->post(route('submissions.store'), [
            'github_owner' => 'laravel',
            'github_repo' => 'framework',
            'pitch' => 'Laravel 是一个优雅的 Web 应用框架。',
        ]);

        $response->assertRedirect(route('submissions.index'));
        $response->assertSessionHas('status', 'submission-pending-review');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'github_owner' => 'laravel',
            'github_repo' => 'framework',
            'status' => SubmissionStatus::PENDING_REVIEW,
        ]);
    }

    /**
     * 命中配置关键词时自动驳回并入库。
     */
    public function test_submission_rejected_auto_on_keyword(): void
    {
        config(['submission.blocked_keywords' => ['赌博']]);

        $user = User::factory()->withGithubBound()->create();

        $response = $this->actingAs($user)->post(route('submissions.store'), [
            'github_owner' => 'laravel',
            'github_repo' => 'framework',
            'pitch' => '涉及赌博内容',
        ]);

        $response->assertRedirect(route('submissions.create'));
        $response->assertSessionHasErrors('auto_rules');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'status' => SubmissionStatus::REJECTED_AUTO,
        ]);
    }
}
// AI-GEN-END
