<?php

namespace App\Services\GitHub;

// AI-GEN-BEGIN
/**
 * 调用 GitHub REST「仓库」接口后的语义状态（供任务写日志/更新快照表）。
 */
enum GitHubRepositoryFetchStatus: string
{
    /** 未配置 Token，不发起 HTTP */
    case Skipped = 'skipped';

    /** 成功拿到 JSON 并映射到快照字段 */
    case Success = 'success';

    /** HTTP 404 */
    case NotFound = 'not_found';

    /** HTTP 403 等权限问题 */
    case Forbidden = 'forbidden';

    /** 网络/解析等其它失败 */
    case Failed = 'failed';
}
// AI-GEN-END
