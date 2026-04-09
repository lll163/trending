<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionCreateGateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未绑定 GitHub 的用户不能打开投稿页。
     */
    public function test_submission_create_forbidden_without_github(): void
    {
        $user = User::factory()->create([
            'github_bound_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('submissions.create'));

        $response->assertForbidden();
    }

    /**
     * 已绑定 GitHub 的用户可打开投稿占位页。
     */
    public function test_submission_create_allowed_with_github_bound(): void
    {
        $user = User::factory()->create([
            'github_bound_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('submissions.create'));

        $response->assertOk();
        $response->assertSee(__('提交审核'), false);
    }
}
// AI-GEN-END
