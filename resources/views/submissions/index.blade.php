{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('我的投稿') }}
            </h2>
            <a href="{{ route('submissions.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('新建投稿') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'submission-pending-review')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已提交，等待人工审核。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($submissions->isEmpty())
                        <p>{{ __('暂无投稿。') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left">
                                        <th class="py-2 pr-4">{{ __('仓库') }}</th>
                                        <th class="py-2 pr-4">{{ __('状态') }}</th>
                                        <th class="py-2 pr-4">{{ __('时间') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submissions as $sub)
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 pr-4 font-mono">{{ $sub->github_owner }}/{{ $sub->github_repo }}</td>
                                            <td class="py-2 pr-4">
                                                @switch($sub->status)
                                                    @case(\App\Models\SubmissionStatus::PENDING_REVIEW)
                                                        <span class="text-amber-700">{{ __('待审核') }}</span>
                                                        @break
                                                    @case(\App\Models\SubmissionStatus::APPROVED)
                                                        <span class="text-green-700">{{ __('已通过') }}</span>
                                                        @break
                                                    @case(\App\Models\SubmissionStatus::REJECTED_AUTO)
                                                        <span class="text-red-600">{{ __('自动驳回') }}</span>
                                                        @break
                                                    @case(\App\Models\SubmissionStatus::REJECTED_REVIEW)
                                                        <span class="text-red-700">{{ __('人工驳回') }}</span>
                                                        @break
                                                    @default
                                                        {{ $sub->status }}
                                                @endswitch
                                            </td>
                                            <td class="py-2 pr-4 text-gray-600">{{ $sub->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @if ($sub->reject_reason)
                                            <tr class="border-b border-gray-100">
                                                <td colspan="3" class="pb-2 text-xs text-gray-500">{{ $sub->reject_reason }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $submissions->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
