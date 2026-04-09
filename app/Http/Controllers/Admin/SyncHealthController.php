<?php

namespace App\Http\Controllers\Admin;

// AI-GEN-BEGIN
use App\Http\Controllers\Controller;
use App\Models\ReposSnapshot;
use Illuminate\View\View;

/**
 * 管理员只读页：列出 `snapshot_sync_error` 非空的仓库快照（便于排查 GitHub 同步问题）。
 */
class SyncHealthController extends Controller
{
    /**
     * 分页展示最近同步失败的快照（按 `updated_at` 降序）。
     */
    public function index(): View
    {
        $perPage = max(5, min(100, (int) config('sync_health.per_page', 25)));

        $failures = ReposSnapshot::query()
            ->whereNotNull('snapshot_sync_error')
            ->where('snapshot_sync_error', '!=', '')
            ->orderByDesc('updated_at')
            ->paginate($perPage);

        return view('admin.sync_health.index', [
            'failures' => $failures,
        ]);
    }
}
// AI-GEN-END
