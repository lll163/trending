{{-- AI-GEN-BEGIN --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('GitHub 绑定') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('绑定后可提交开源项目收录申请。') }}
        </p>
    </header>

    @if (session('status') === 'github-bound')
        <p class="mt-2 text-sm font-medium text-green-600">
            {{ __('GitHub 绑定成功。') }}
        </p>
    @endif

    <div class="mt-6 space-y-2">
        @if ($user->github_bound_at)
            <p class="text-sm text-gray-700">
                {{ __('已绑定，绑定时间：') }}{{ $user->github_bound_at->format('Y-m-d H:i') }}
            </p>
            @php
                $githubAccount = $user->oauthAccounts->firstWhere('provider', 'github');
            @endphp
            @if ($githubAccount && $githubAccount->provider_login)
                <p class="text-sm text-gray-600">{{ __('GitHub 用户：') }}{{ $githubAccount->provider_login }}</p>
            @endif
        @else
            <a href="{{ route('github.redirect') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('使用 GitHub 授权绑定') }}
            </a>
        @endif

        <x-input-error class="mt-2" :messages="$errors->get('github')" />
    </div>
</section>
{{-- AI-GEN-END --}}
