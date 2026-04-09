<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Services\Submission\AutoRulePipeline;
use App\Services\Submission\SubmissionDraft;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * 用户投稿：列表、创建、提交（自动规则 + 状态机）。
 */
class SubmissionController extends Controller
{
    /**
     * 当前登录用户自己的投稿列表。
     */
    public function index(): View
    {
        $this->authorize('viewAny', Submission::class);

        $submissions = Submission::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('submissions.index', ['submissions' => $submissions]);
    }

    /**
     * 展示投稿表单（需已绑定 GitHub）。
     */
    public function create(): View
    {
        $this->authorize('create', Submission::class);

        return view('submissions.create');
    }

    /**
     * 保存投稿：自动规则通过则 pending_review，否则 rejected_auto 并保留原因。
     */
    public function store(StoreSubmissionRequest $request, AutoRulePipeline $pipeline): RedirectResponse
    {
        $draft = SubmissionDraft::fromValidated($request->validated());
        $result = $pipeline->evaluate($draft);

        $payload = [
            'user_id' => (int) $request->user()->id,
            'github_owner' => $draft->githubOwner,
            'github_repo' => $draft->githubRepo,
            'pitch' => $draft->pitch,
            'status' => $result->passed ? SubmissionStatus::PENDING_REVIEW : SubmissionStatus::REJECTED_AUTO,
            'reject_reason' => $result->passed ? null : $result->summaryMessage(),
        ];

        Submission::query()->create($payload);

        if ($result->passed) {
            return redirect()
                ->route('submissions.index')
                ->with('status', 'submission-pending-review');
        }

        return redirect()
            ->route('submissions.create')
            ->withErrors(['auto_rules' => $result->messages])
            ->withInput();
    }
}
// AI-GEN-END
