{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('标签管理') }}
            </h2>
            <a href="{{ route('admin.tags.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('新建标签') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'tag-created')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已创建标签。') }}</div>
            @endif
            @if (session('status') === 'tag-updated')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已更新标签。') }}</div>
            @endif
            @if (session('status') === 'tag-deleted')
                <div class="mb-4 p-4 bg-amber-50 text-amber-900 rounded-md">{{ __('已删除标签。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($tags->isEmpty())
                        <p class="text-gray-600">{{ __('暂无标签，请先新建。') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b">
                                        <th class="pb-2 pe-4">{{ __('slug') }}</th>
                                        <th class="pb-2 pe-4">{{ __('标题') }}</th>
                                        <th class="pb-2 w-40"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($tags as $tag)
                                        <tr>
                                            <td class="py-3 pe-4 font-mono text-gray-800">{{ $tag->slug }}</td>
                                            <td class="py-3 pe-4">{{ $tag->title }}</td>
                                            <td class="py-3 text-end space-x-3 whitespace-nowrap">
                                                <a href="{{ route('admin.tags.edit', $tag) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('编辑') }}</a>
                                                <form method="post" action="{{ route('admin.tags.destroy', $tag) }}" class="inline" onsubmit="return confirm('{{ __('确定删除该标签？') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">{{ __('删除') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $tags->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
