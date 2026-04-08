<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;

/**
 * 投稿自动规则用黑名单条目。
 */
class BlacklistEntry extends Model
{
    protected $table = 'blacklist_entries';

    protected $fillable = [
        'pattern_type',
        'pattern_value',
    ];
}
// AI-GEN-END
