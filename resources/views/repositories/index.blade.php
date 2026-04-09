{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', __('开源仓库').' — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ __('开源仓库') }}</h1>
    <p class="text-sm text-gray-600 mb-6">{{ __('展示平台已收录的项目（数据来自本地快照）。') }}</p>

    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('repositories.index') }}"
           class="px-3 py-1 rounded-full text-sm {{ $activeTag === '' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            {{ __('全部') }}
        </a>
        @foreach ($tags as $tag)
            <a href="{{ route('repositories.index', ['tag' => $tag->slug]) }}"
               class="px-3 py-1 rounded-full text-sm {{ $activeTag === $tag->slug ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                {{ $tag->title }}
            </a>
        @endforeach
    </div>

    @if ($snapshots->isEmpty())
        <p class="text-gray-600">{{ __('暂无符合条件的仓库。') }}</p>
    @else
        <ul class="divide-y divide-gray-200 bg-white rounded-lg shadow">
            @foreach ($snapshots as $s)
                <li class="p-4 hover:bg-gray-50">
                    <a href="{{ route('repositories.show', ['owner' => $s->github_owner, 'repo' => $s->github_repo]) }}"
                       class="font-mono text-indigo-700 hover:text-indigo-900">
                        {{ $s->github_owner }}/{{ $s->github_repo }}
                    </a>
                    <span class="text-sm text-gray-500 ms-2">★ {{ $s->stars_cnt }}</span>
                    @if ($s->description)
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $s->description }}</p>
                    @endif
                    @if ($s->tags->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($s->tags as $t)
                                <span class="text-xs px-2 py-0.5 bg-gray-100 rounded">{{ $t->title }}</span>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $snapshots->links() }}</div>
    @endif
@endsection
{{-- AI-GEN-END --}}
