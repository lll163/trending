{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', $snapshot->github_owner.'/'.$snapshot->github_repo.' — '.config('app.name'))

@section('content')
    <div class="mb-4">
        <a href="{{ route('repositories.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('← 返回列表') }}</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-mono font-semibold text-gray-900">
            {{ $snapshot->github_owner }}/{{ $snapshot->github_repo }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            ★ {{ $snapshot->stars_cnt }}
            @if ($snapshot->forks_cnt)
                · {{ __('Fork') }} {{ $snapshot->forks_cnt }}
            @endif
        </p>

        @if ($snapshot->tags->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($snapshot->tags as $t)
                    <a href="{{ route('repositories.index', ['tag' => $t->slug]) }}"
                       class="text-xs px-2 py-1 bg-indigo-50 text-indigo-800 rounded hover:bg-indigo-100">
                        {{ $t->title }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($snapshot->description)
            <p class="mt-4 text-gray-700 whitespace-pre-wrap">{{ $snapshot->description }}</p>
        @endif

        @if ($snapshot->homepage_url)
            <p class="mt-4">
                <a href="{{ $snapshot->homepage_url }}" class="text-indigo-600 hover:underline" target="_blank" rel="noopener noreferrer">{{ __('项目主页') }}</a>
            </p>
        @endif

        <p class="mt-6 text-xs text-gray-400">
            {{ __('GitHub:') }}
            <a class="text-indigo-500 hover:underline" target="_blank" rel="noopener noreferrer"
               href="https://github.com/{{ $snapshot->github_owner }}/{{ $snapshot->github_repo }}">
                github.com/{{ $snapshot->github_owner }}/{{ $snapshot->github_repo }}
            </a>
        </p>
    </div>
@endsection
{{-- AI-GEN-END --}}
