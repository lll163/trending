{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('仓库快照') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'repos-tags-updated')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已更新该快照的标签。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="get" action="{{ route('admin.repos-snapshots.index') }}" class="flex flex-wrap gap-3 items-end">
                        <div class="grow min-w-[200px]">
                            <x-input-label for="q" :value="__('搜索 owner / repo')" />
                            <x-text-input id="q" name="q" type="text" class="mt-1 block w-full font-mono"
                                :value="$search" autocomplete="off" placeholder="laravel / framework" />
                        </div>
                        <x-primary-button type="submit">{{ __('搜索') }}</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($snapshots->isEmpty())
                        <p class="text-gray-600">{{ __('没有匹配的快照。') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($snapshots as $s)
                                <li class="py-4 flex flex-wrap justify-between gap-4 items-start">
                                    <div>
                                        <span class="font-mono text-sm">{{ $s->github_owner }}/{{ $s->github_repo }}</span>
                                        @if (! $s->is_published)
                                            <span class="ms-2 text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-900">{{ __('未发布') }}</span>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">★ {{ $s->stars_cnt }}</p>
                                        @if ($s->tags->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach ($s->tags as $t)
                                                    <span class="text-xs px-2 py-0.5 bg-gray-100 rounded">{{ $t->title }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.repos-snapshots.edit-tags', $s) }}" class="text-sm text-indigo-600 hover:text-indigo-900 shrink-0">{{ __('编辑标签') }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">{{ $snapshots->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
