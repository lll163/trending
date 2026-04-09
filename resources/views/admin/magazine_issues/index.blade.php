{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('月刊期号') }}
            </h2>
            <a href="{{ route('admin.magazine-issues.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('新建期号') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'magazine-issue-created')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已创建期号。') }}</div>
            @endif
            @if (session('status') === 'magazine-issue-updated')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已更新期号。') }}</div>
            @endif
            @if (session('status') === 'magazine-issue-deleted')
                <div class="mb-4 p-4 bg-amber-50 text-amber-900 rounded-md">{{ __('已删除期号。') }}</div>
            @endif
            @if (session('status') === 'magazine-issue-delete-blocked')
                <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-md">{{ __('期内仍有文章，请先删除或转移文章后再删除期号。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($issues->isEmpty())
                        <p class="text-gray-600">{{ __('暂无期号。') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($issues as $issue)
                                <li class="py-4 flex flex-wrap justify-between gap-4 items-center">
                                    <div>
                                        <span class="font-mono text-sm text-indigo-700">{{ $issue->issue_code }}</span>
                                        <span class="ms-2 font-medium">{{ $issue->title }}</span>
                                        <span class="ms-2 text-xs px-2 py-0.5 rounded {{ $issue->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $issue->status }}</span>
                                    </div>
                                    <div class="flex gap-3 text-sm shrink-0">
                                        <a href="{{ route('admin.magazine-issues.edit', $issue) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('编辑') }}</a>
                                        <form method="post" action="{{ route('admin.magazine-issues.destroy', $issue) }}" class="inline" onsubmit="return confirm('{{ __('确定删除该期号？') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">{{ __('删除') }}</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">{{ $issues->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
