{{-- AI-GEN-BEGIN --}}
@extends('layouts.site')

@section('title', $article->title.' — '.$issue->issue_code.' — '.config('app.name'))

@section('content')
    <div class="mb-6 flex flex-wrap gap-4 text-sm">
        <a href="{{ route('magazines.show', $issue->issue_code) }}" class="text-indigo-600 hover:text-indigo-800">← {{ $issue->issue_code }}</a>
        <a href="{{ route('magazines.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('月刊列表') }}</a>
    </div>

    <article class="bg-white rounded-lg shadow p-6 sm:p-8">
        <header class="mb-6 border-b border-gray-100 pb-4">
            <p class="text-xs font-mono text-indigo-600">{{ $issue->issue_code }}</p>
            <h1 class="text-2xl font-semibold text-gray-900 mt-1">{{ $article->title }}</h1>
            @if ($article->published_at)
                <p class="text-xs text-gray-500 mt-2">{{ $article->published_at->format('Y-m-d H:i') }}</p>
            @endif
        </header>

        {{-- 由 Markdown 转换的 HTML；服务端已 strip 未信任 HTML --}}
        <div class="max-w-none text-gray-800 leading-relaxed space-y-4 [&_h1]:text-2xl [&_h1]:font-bold [&_h2]:text-xl [&_h2]:font-semibold [&_p]:my-3 [&_ul]:list-disc [&_ul]:ms-6 [&_ol]:list-decimal [&_ol]:ms-6 [&_a]:text-indigo-600 [&_a]:underline [&_code]:bg-gray-100 [&_code]:px-1 [&_code]:rounded [&_pre]:bg-gray-900 [&_pre]:text-gray-100 [&_pre]:p-4 [&_pre]:rounded-lg [&_pre]:overflow-x-auto">
            {!! $bodyHtml !!}
        </div>
    </article>
@endsection
{{-- AI-GEN-END --}}
