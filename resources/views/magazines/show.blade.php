{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', $issue->title.' — '.config('app.name'))

@section('content')
    <div class="mb-6">
        <a href="{{ route('magazines.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('← 月刊列表') }}</a>
    </div>

    <header class="mb-8">
        @if ($issue->cover_path)
            <img src="{{ asset('storage/'.$issue->cover_path) }}" alt="" class="w-full max-h-64 object-cover rounded-lg shadow mb-4">
        @endif
        <p class="text-sm font-mono text-indigo-600">{{ $issue->issue_code }}</p>
        <h1 class="text-2xl font-semibold text-gray-900 mt-1">{{ $issue->title }}</h1>
        @if ($issue->published_at)
            <p class="text-sm text-gray-500 mt-2">{{ $issue->published_at->format('Y-m-d') }}</p>
        @endif
    </header>

    <h2 class="text-lg font-medium text-gray-800 mb-4">{{ __('本期目录') }}</h2>

    @if ($articles->isEmpty())
        <p class="text-gray-600">{{ __('本期暂无已发布文章。') }}</p>
    @else
        <ol class="list-decimal list-inside space-y-3 text-gray-800">
            @foreach ($articles as $a)
                <li>
                    <a href="{{ route('magazines.article', ['issue_code' => $issue->issue_code, 'article_slug' => $a->slug]) }}"
                       class="text-indigo-700 hover:text-indigo-900 font-medium">
                        {{ $a->title }}
                    </a>
                </li>
            @endforeach
        </ol>
    @endif
@endsection
{{-- AI-GEN-END --}}
