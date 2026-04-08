<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // AI-GEN-BEGIN
        'is_admin',
        'github_bound_at',
        // AI-GEN-END
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // AI-GEN-BEGIN
            'is_admin' => 'boolean',
            'github_bound_at' => 'datetime',
            // AI-GEN-END
        ];
    }

    // AI-GEN-BEGIN
    /**
     * 用户绑定的 OAuth 账户列表。
     *
     * @return HasMany<OauthAccount, $this>
     */
    public function oauthAccounts(): HasMany
    {
        return $this->hasMany(OauthAccount::class, 'user_id');
    }

    /**
     * 用户提交的收录申请。
     *
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    /**
     * 作为管理员产生的审核日志。
     *
     * @return HasMany<ReviewLog, $this>
     */
    public function reviewLogsAsAdmin(): HasMany
    {
        return $this->hasMany(ReviewLog::class, 'admin_user_id');
    }
    // AI-GEN-END
}
