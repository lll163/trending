{{-- AI-GEN-BEGIN --}}
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">{{ __('首页') }}</a>
            <a href="{{ route('repositories.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">{{ __('开源仓库') }}</a>
        </div>
        <div class="flex items-center gap-4 text-sm">
            @auth
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">{{ __('控制台') }}</a>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('登录') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">{{ __('注册') }}</a>
                @endif
            @endauth
        </div>
    </div>
</nav>
{{-- AI-GEN-END --}}
