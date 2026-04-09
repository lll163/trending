{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('同步失败摘要') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="mb-6 text-sm text-gray-600">
                {{ __('以下为 `snapshot_sync_error` 非空的仓库快照，按最近更新时间排序。清空错误需在下次同步成功或手动改库。') }}
            </p>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($failures->isEmpty())
                        <p class="text-gray-600">{{ __('当前没有记录同步错误。') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b">
                                        <th class="pb-2 pe-4">{{ __('仓库') }}</th>
                                        <th class="pb-2 pe-4">{{ __('错误摘要') }}</th>
                                        <th class="pb-2 pe-4">{{ __('更新时间') }}</th>
                                        <th class="pb-2 w-28"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($failures as $row)
                                        <tr class="align-top">
                                            <td class="py-3 pe-4 font-mono text-xs whitespace-nowrap">
                                                {{ $row->github_owner }}/{{ $row->github_repo }}
                                            </td>
                                            <td class="py-3 pe-4 text-gray-800 break-all max-w-xl">
                                                {{ $row->snapshot_sync_error }}
                                            </td>
                                            <td class="py-3 pe-4 text-gray-500 whitespace-nowrap text-xs">
                                                {{ $row->updated_at?->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="py-3 text-end">
                                                <a href="{{ route('admin.repos-snapshots.edit-tags', $row) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('标签') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $failures->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
