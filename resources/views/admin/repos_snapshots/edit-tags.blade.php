{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('编辑快照标签') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="mb-4 text-sm text-gray-600 font-mono">
                {{ $reposSnapshot->github_owner }}/{{ $reposSnapshot->github_repo }}
            </p>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-xl">
                    <form method="post" action="{{ route('admin.repos-snapshots.update-tags', $reposSnapshot) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <fieldset>
                            <legend class="text-sm font-medium text-gray-700">{{ __('选择标签（可多选）') }}</legend>
                            <div class="mt-3 space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                                @forelse ($tags as $t)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="tag_ids[]" value="{{ $t->id }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            @checked($reposSnapshot->tags->contains('id', $t->id))>
                                        <span>{{ $t->title }}</span>
                                        <span class="text-xs text-gray-400 font-mono">({{ $t->slug }})</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">{{ __('暂无标签，请先在「标签」中创建。') }}</p>
                                @endforelse
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('tag_ids')" />
                        </fieldset>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                            <a href="{{ route('admin.repos-snapshots.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回列表') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
