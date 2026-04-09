{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('审核投稿') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-2">
                <p><span class="text-gray-500">{{ __('仓库') }}：</span><span class="font-mono">{{ $submission->github_owner }}/{{ $submission->github_repo }}</span></p>
                <p><span class="text-gray-500">{{ __('投稿人') }}：</span>{{ $submission->user->name }} ({{ $submission->user->email }})</p>
                <div>
                    <p class="text-gray-500">{{ __('推荐语') }}</p>
                    <p class="mt-1 whitespace-pre-wrap text-gray-900">{{ $submission->pitch }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4">
                <form method="post" action="{{ route('admin.submissions.approve', $submission) }}">
                    @csrf
                    <x-primary-button type="submit">{{ __('通过') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-2">{{ __('驳回') }}</h3>
                <form method="post" action="{{ route('admin.submissions.reject', $submission) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="remark" :value="__('驳回原因')" />
                        <textarea id="remark" name="remark" rows="3" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('remark') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>
                    <x-secondary-button type="submit">{{ __('提交驳回') }}</x-secondary-button>
                </form>
            </div>

            <a href="{{ route('admin.submissions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回队列') }}</a>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
