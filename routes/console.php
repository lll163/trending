<?php

use App\Jobs\SyncAllRepositoriesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// AI-GEN-BEGIN
Artisan::command('github:sync-repository-snapshots', function (): void {
    $this->info('开始同步已发布仓库的 GitHub 元数据…');
    SyncAllRepositoriesJob::dispatchSync();
    $this->info('已派发/执行完毕（取决于队列驱动）。');
})->purpose('立即执行一次与定时任务相同的仓库快照批量同步');

/*
|--------------------------------------------------------------------------
| 定时任务
|--------------------------------------------------------------------------
| 每日 03:00（app 时区）同步 `is_published=1` 的快照。单条失败写入 `snapshot_sync_error`，不中断整批。
| 服务器 Cron：* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
| 说明：`php artisan schedule:list` 会探测互斥锁；若 `.env` 中 CACHE_STORE=database 且库未就绪，可能报错。
| 可将 CACHE_STORE 改为 file/array，或完成 migrate 并具备可用数据库后再执行 schedule:list。
| 生产环境可为任务链式追加 ->name('...')->withoutOverlapping()（需可用 cache 后端）。
*/
Schedule::job(new SyncAllRepositoriesJob)->dailyAt('03:00');
// AI-GEN-END
