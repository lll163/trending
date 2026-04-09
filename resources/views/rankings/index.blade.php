{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', __('榜单').' — '.config('app.name'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('榜单') }}</h1>
            <p class="text-sm text-gray-500 mt-1 font-mono">{{ $rankingKey }}</p>
        </div>
        <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('← 首页') }}</a>
    </div>

    @if ($fallbackSnapshots !== null)
        <p class="mb-4 text-sm text-amber-800 bg-amber-50 border border-amber-100 rounded-md px-3 py-2">
            {{ __('榜单尚未重算，以下为按 Star 数实时排序的预览。可执行 php artisan ranking:rebuild 或等待定时任务。') }}
        </p>
        <ul class="bg-white rounded-lg shadow divide-y divide-gray-100">
            @foreach ($fallbackSnapshots as $s)
                <li class="p-4 flex flex-wrap justify-between gap-2 items-center">
                    <a href="{{ route('repositories.show', ['owner' => $s->github_owner, 'repo' => $s->github_repo]) }}"
                       class="font-mono text-indigo-700 hover:text-indigo-900">
                        {{ $s->github_owner }}/{{ $s->github_repo }}
                    </a>
                    <span class="text-sm text-gray-600">★ {{ $s->stars_cnt }}</span>
                </li>
            @endforeach
        </ul>
        <div class="mt-6">{{ $fallbackSnapshots->links() }}</div>
    @else
        <ul class="bg-white rounded-lg shadow divide-y divide-gray-100">
            @foreach ($entries as $entry)
                @php $s = $entry->reposSnapshot @endphp
                @if ($s)
                    <li class="p-4 flex flex-wrap justify-between gap-2 items-center">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-mono text-gray-400 w-8">{{ $entry->position }}</span>
                            <a href="{{ route('repositories.show', ['owner' => $s->github_owner, 'repo' => $s->github_repo]) }}"
                               class="font-mono text-indigo-700 hover:text-indigo-900">
                                {{ $s->github_owner }}/{{ $s->github_repo }}
                            </a>
                        </div>
                        <span class="text-sm text-gray-600">★ {{ $s->stars_cnt }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="mt-6">{{ $entries->links() }}</div>
    @endif
@endsection
{{-- AI-GEN-END --}}
