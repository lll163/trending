<?php

// AI-GEN-BEGIN
return [

    /*
    |--------------------------------------------------------------------------
    | 默认榜单键（与 ranking_configs.ranking_key / ranking_entries.ranking_key 一致）
    |--------------------------------------------------------------------------
    */
    'default_key' => env('RANKING_DEFAULT_KEY', 'stars_public'),

    /*
    |--------------------------------------------------------------------------
    | 新建 ranking_configs 行时使用的默认 JSON 参数
    |--------------------------------------------------------------------------
    |
    | limit: 榜单最大条数
    | min_updated_days: 仅收录 updated_at 在 N 天内的快照；null 表示不限制
    */
    'default_params' => [
        'limit' => 30,
        'min_updated_days' => null,
    ],
];
// AI-GEN-END
