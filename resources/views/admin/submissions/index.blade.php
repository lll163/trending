{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('投稿审核队列') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'submission-approved')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已通过一条投稿。') }}</div>
            @endif
            @if (session('status') === 'submission-rejected')
                <div class="mb-4 p-4 bg-amber-50 text-amber-900 rounded-md">{{ __('已驳回一条投稿。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($submissions->isEmpty())
                        <p class="text-gray-600">{{ __('当前没有待审核投稿。') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($submissions as $sub)
                                <li class="py-3 flex justify-between items-center gap-4">
                                    <div>
                                        <span class="font-mono text-sm">{{ $sub->github_owner }}/{{ $sub->github_repo }}</span>
                                        <span class="text-xs text-gray-500 ms-2">{{ $sub->user->name ?? '' }}</span>
                                    </div>
                                    <a href="{{ route('admin.submissions.show', $sub) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('审核') }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">{{ $submissions->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
