{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', config('app.name').' — '.__('首页'))

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">{{ config('app.name') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('发现优质开源项目，阅读月刊与榜单。') }}</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <section class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('推荐仓库') }}</h2>
                <a href="{{ route('rankings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('完整榜单') }} →</a>
            </div>
            @if ($rankingSnapshots->isEmpty())
                <p class="text-sm text-gray-500">{{ __('暂无已发布仓库，请稍后再来。') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($rankingSnapshots as $s)
                        <li class="py-3">
                            <a href="{{ route('repositories.show', ['owner' => $s->github_owner, 'repo' => $s->github_repo]) }}"
                               class="font-mono text-sm text-indigo-700 hover:text-indigo-900">
                                {{ $s->github_owner }}/{{ $s->github_repo }}
                            </a>
                            <span class="text-xs text-gray-500 ms-2">★ {{ $s->stars_cnt }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('最新收录') }}</h2>
            <p class="text-xs text-gray-500 mb-3">{{ __('最近审核通过的投稿。') }}</p>
            @if ($latestApprovedSubmissions->isEmpty())
                <p class="text-sm text-gray-500">{{ __('暂无通过记录。') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($latestApprovedSubmissions as $sub)
                        <li class="py-3">
                            <a href="{{ route('repositories.show', ['owner' => $sub->github_owner, 'repo' => $sub->github_repo]) }}"
                               class="font-mono text-sm text-indigo-700 hover:text-indigo-900">
                                {{ $sub->github_owner }}/{{ $sub->github_repo }}
                            </a>
                            @if ($sub->user)
                                <span class="text-xs text-gray-500 ms-2">{{ $sub->user->name }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <section class="mt-8 bg-indigo-50 rounded-lg p-6 border border-indigo-100">
        <h2 class="text-sm font-semibold text-indigo-900 mb-3">{{ __('浏览更多') }}</h2>
        <div class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('repositories.index') }}" class="text-indigo-700 hover:underline">{{ __('开源仓库') }}</a>
            <span class="text-indigo-300">|</span>
            <a href="{{ route('magazines.index') }}" class="text-indigo-700 hover:underline">{{ __('月刊') }}</a>
            <span class="text-indigo-300">|</span>
            <a href="{{ route('rankings.index') }}" class="text-indigo-700 hover:underline">{{ __('Star 榜单') }}</a>
        </div>
    </section>
@endsection
{{-- AI-GEN-END --}}
