<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\ReposSnapshot;
use App\Models\ReviewLog;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * 管理员审核投稿队列（通过 / 驳回 + 审计日志）。
 */
class SubmissionReviewController extends Controller
{
    /**
     * 待人工审核列表。
     */
    public function index(): View
    {
        $submissions = Submission::query()
            ->where('status', SubmissionStatus::PENDING_REVIEW)
            ->latest()
            ->with('user')
            ->paginate(20);

        return view('admin.submissions.index', ['submissions' => $submissions]);
    }

    /**
     * 单条待审详情。
     */
    public function show(Submission $submission): View
    {
        abort_unless($submission->status === SubmissionStatus::PENDING_REVIEW, 404);
        $submission->load('user');

        return view('admin.submissions.show', ['submission' => $submission]);
    }

    /**
     * 审核通过：创建或关联 repos_snapshots，投稿标记为 approved。
     */
    public function approve(Request $request, Submission $submission): RedirectResponse
    {
        abort_unless($submission->status === SubmissionStatus::PENDING_REVIEW, 404);

        DB::transaction(function () use ($request, $submission): void {
            $snapshot = ReposSnapshot::query()->firstOrCreate(
                [
                    'github_owner' => $submission->github_owner,
                    'github_repo' => $submission->github_repo,
                ],
                [
                    'description' => '',
                    'stars_cnt' => 0,
                    'forks_cnt' => 0,
                    'is_published' => true,
                ]
            );

            $submission->update([
                'status' => SubmissionStatus::APPROVED,
                'repos_snapshot_id' => $snapshot->id,
                'reject_reason' => null,
            ]);

            ReviewLog::query()->create([
                'submission_id' => $submission->id,
                'admin_user_id' => (int) $request->user()->id,
                'action' => 'approve',
                'remark' => null,
            ]);
        });

        return redirect()
            ->route('admin.submissions.index')
            ->with('status', 'submission-approved');
    }

    /**
     * 审核驳回：记录原因与 review_logs。
     */
    public function reject(Request $request, Submission $submission): RedirectResponse
    {
        abort_unless($submission->status === SubmissionStatus::PENDING_REVIEW, 404);

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $submission, $validated): void {
            $submission->update([
                'status' => SubmissionStatus::REJECTED_REVIEW,
                'reject_reason' => $validated['remark'],
            ]);

            ReviewLog::query()->create([
                'submission_id' => $submission->id,
                'admin_user_id' => (int) $request->user()->id,
                'action' => 'reject',
                'remark' => $validated['remark'],
            ]);
        });

        return redirect()
            ->route('admin.submissions.index')
            ->with('status', 'submission-rejected');
    }
}
// AI-GEN-END
