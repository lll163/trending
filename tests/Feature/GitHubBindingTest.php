<?php

namespace Tests\Feature;

// AI-GEN-BEGIN
use App\Models\OauthAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GitHubBindingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 已登录用户跳转 GitHub 授权时应重定向至 OAuth 地址。
     */
    public function test_authenticated_user_github_redirect(): void
    {
        $user = User::factory()->create();

        $provider = Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')->once()->with('github')->andReturn($provider);

        $response = $this->actingAs($user)->get(route('github.redirect'));

        $response->assertRedirect('https://github.com/login/oauth/authorize');
    }

    /**
     * 回调成功后应写入 oauth 并设置 github_bound_at。
     */
    public function test_github_callback_binds_user(): void
    {
        $user = User::factory()->create();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('424242');
        $socialiteUser->shouldReceive('getNickname')->andReturn('demo-dev');
        $socialiteUser->token = 'fake-access-token';
        $socialiteUser->refreshToken = null;

        $provider = Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->once()->with('github')->andReturn($provider);

        $response = $this->actingAs($user)->get(route('github.callback'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'github-bound');

        $this->assertDatabaseHas('oauth_accounts', [
            'user_id' => $user->id,
            'provider' => 'github',
            'provider_user_id' => '424242',
            'provider_login' => 'demo-dev',
        ]);

        $user->refresh();
        $this->assertNotNull($user->github_bound_at);
    }

    /**
     * 若 GitHub 账户已被他人绑定，当前用户应收到错误提示。
     */
    public function test_github_callback_rejects_duplicate_provider_account(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        OauthAccount::query()->create([
            'user_id' => $owner->id,
            'provider' => 'github',
            'provider_user_id' => '999',
            'provider_login' => 'taken',
            'access_token' => null,
            'refresh_token' => null,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('999');
        $socialiteUser->shouldReceive('getNickname')->andReturn('taken');
        $socialiteUser->token = 'x';
        $socialiteUser->refreshToken = null;

        $provider = Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->once()->with('github')->andReturn($provider);

        $response = $this->actingAs($other)->get(route('github.callback'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('github');

        $this->assertDatabaseMissing('oauth_accounts', [
            'user_id' => $other->id,
            'provider' => 'github',
        ]);
    }
}
// AI-GEN-END
