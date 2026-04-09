<?php

// AI-GEN-BEGIN
$env = env('SUBMISSION_BLOCKED_KEYWORDS');

if ($env === null || $env === '') {
    $blocked = ['赌博', '色情'];
} else {
    $parts = explode(',', (string) $env);
    $blocked = array_values(array_filter(array_map(
        static fn (string $s): string => trim($s),
        $parts
    )));
}

return [
    /**
     * 推荐语简单关键词拦截（可 env 逗号分隔覆盖默认列表）
     */
    'blocked_keywords' => $blocked,
];
// AI-GEN-END
