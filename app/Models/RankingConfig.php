<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;

/**
 * 榜单计算参数配置。
 */
class RankingConfig extends Model
{
    protected $table = 'ranking_configs';

    protected $fillable = [
        'ranking_key',
        'params_json',
        'last_computed_at',
    ];

    protected function casts(): array
    {
        return [
            'params_json' => 'array',
            'last_computed_at' => 'datetime',
        ];
    }
}
// AI-GEN-END
