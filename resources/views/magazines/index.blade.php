{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', __('月刊').' — '.config('app.name'))

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ __('月刊') }}</h1>
    <p class="text-sm text-gray-600 mb-8">{{ __('已发布的期号列表。') }}</p>

    @if ($issues->isEmpty())
        <p class="text-gray-600">{{ __('暂无已发布期号。') }}</p>
    @else
        <ul class="grid gap-4 sm:grid-cols-2">
            @foreach ($issues as $issue)
                <li class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow">
                    <a href="{{ route('magazines.show', $issue->issue_code) }}" class="block">
                        @if ($issue->cover_path)
                            <img src="{{ asset('storage/'.$issue->cover_path) }}" alt="" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400 text-sm">{{ __('无封面') }}</div>
                        @endif
                        <div class="p-4">
                            <span class="text-xs font-mono text-indigo-600">{{ $issue->issue_code }}</span>
                            <h2 class="font-semibold text-gray-900 mt-1">{{ $issue->title }}</h2>
                            @if ($issue->published_at)
                                <p class="text-xs text-gray-500 mt-2">{{ $issue->published_at->format('Y-m-d') }}</p>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mt-8">{{ $issues->links() }}</div>
    @endif
@endsection
{{-- AI-GEN-END --}}
