<?php

namespace App\Http\Requests;

// AI-GEN-BEGIN
use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 校验投稿表单原始字段（业务规则由 AutoRulePipeline 处理）。
 */
class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Submission::class) ?? false;
    }

    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        return [
            'github_owner' => ['required', 'string', 'max:255'],
            'github_repo' => ['required', 'string', 'max:255'],
            'pitch' => ['required', 'string', 'max:10000'],
        ];
    }
}
// AI-GEN-END
