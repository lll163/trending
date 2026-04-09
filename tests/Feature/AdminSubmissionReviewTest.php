<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\ReviewLog;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 非管理员不能访问审核队列。
     */
    public function test_guest_cannot_access_admin_queue(): void
    {
        $user = User::factory()->withGithubBound()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.submissions.index'))->assertForbidden();
    }

    /**
     * 管理员可通过投稿并生成快照与审计日志。
     */
    public function test_admin_can_approve_submission(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->withGithubBound()->create();

        $submission = Submission::query()->create([
            'user_id' => $author->id,
            'github_owner' => 'octo',
            'github_repo' => 'hello-world',
            'pitch' => '示例项目',
            'status' => SubmissionStatus::PENDING_REVIEW,
            'reject_reason' => null,
            'repos_snapshot_id' => null,
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.submissions.approve', $submission)
        );

        $response->assertRedirect(route('admin.submissions.index'));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::APPROVED, $submission->status);
        $this->assertNotNull($submission->repos_snapshot_id);

        $this->assertDatabaseHas('repos_snapshots', [
            'github_owner' => 'octo',
            'github_repo' => 'hello-world',
        ]);

        $this->assertSame(1, ReviewLog::query()->where('submission_id', $submission->id)->where('action', 'approve')->count());
    }

    /**
     * 管理员可驳回并写入原因。
     */
    public function test_admin_can_reject_submission(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->withGithubBound()->create();

        $submission = Submission::query()->create([
            'user_id' => $author->id,
            'github_owner' => 'foo',
            'github_repo' => 'bar',
            'pitch' => 'test',
            'status' => SubmissionStatus::PENDING_REVIEW,
            'reject_reason' => null,
            'repos_snapshot_id' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.submissions.reject', $submission), [
            'remark' => '不符合收录方向',
        ]);

        $response->assertRedirect(route('admin.submissions.index'));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::REJECTED_REVIEW, $submission->status);
        $this->assertSame('不符合收录方向', $submission->reject_reason);

        $this->assertSame(1, ReviewLog::query()->where('submission_id', $submission->id)->where('action', 'reject')->count());
    }
}
// AI-GEN-END
