<?php

namespace App\Models;

// AI-GEN-BEGIN
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 第三方 OAuth 账户，与 User 逻辑关联（数据库无外键）。
 */
class OauthAccount extends Model
{
    protected $table = 'oauth_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'provider_login',
        'access_token',
        'refresh_token',
    ];

    /**
     * 所属站内用户。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
// AI-GEN-END
