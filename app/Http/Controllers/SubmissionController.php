<?php

namespace App\Http\Controllers;

// AI-GEN-BEGIN
use App\Models\Submission;
use Illuminate\View\View;

/**
 * 用户投稿入口（表单与校验将在后续任务完善）。
 */
class SubmissionController extends Controller
{
    /**
     * 展示投稿表单（需已绑定 GitHub）。
     */
    public function create(): View
    {
        $this->authorize('create', Submission::class);

        return view('submissions.create');
    }
}
// AI-GEN-END
