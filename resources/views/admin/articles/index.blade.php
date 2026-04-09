{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('文章') }}
            </h2>
            <a href="{{ route('admin.articles.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('新建文章') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status') === 'article-created')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已创建文章。') }}</div>
            @endif
            @if (session('status') === 'article-updated')
                <div class="mb-4 p-4 bg-green-50 text-green-800 rounded-md">{{ __('已更新文章。') }}</div>
            @endif
            @if (session('status') === 'article-deleted')
                <div class="mb-4 p-4 bg-amber-50 text-amber-900 rounded-md">{{ __('已删除文章。') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="get" action="{{ route('admin.articles.index') }}" class="flex flex-wrap gap-3 items-end">
                        <div>
                            <x-input-label for="magazine_issue_id" :value="__('按期刊筛选')" />
                            <select id="magazine_issue_id" name="magazine_issue_id" class="mt-1 block w-full min-w-[200px] border-gray-300 rounded-md shadow-sm">
                                <option value="" @selected($filterIssueId === null)>{{ __('全部') }}</option>
                                @foreach ($issues as $iss)
                                    <option value="{{ $iss->id }}" @selected($filterIssueId === $iss->id)>
                                        {{ $iss->issue_code }} — {{ $iss->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button type="submit">{{ __('筛选') }}</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    @if ($articles->isEmpty())
                        <p class="text-gray-600">{{ __('暂无文章。') }}</p>
                    @else
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="pb-2 pe-4">{{ __('slug') }}</th>
                                    <th class="pb-2 pe-4">{{ __('标题') }}</th>
                                    <th class="pb-2 pe-4">{{ __('期刊') }}</th>
                                    <th class="pb-2 pe-4">{{ __('状态') }}</th>
                                    <th class="pb-2 w-32"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($articles as $a)
                                    <tr>
                                        <td class="py-3 pe-4 font-mono text-xs">{{ $a->slug }}</td>
                                        <td class="py-3 pe-4">{{ $a->title }}</td>
                                        <td class="py-3 pe-4 text-gray-600">{{ $a->magazineIssue?->issue_code ?? '—' }}</td>
                                        <td class="py-3 pe-4">{{ $a->status }}</td>
                                        <td class="py-3 text-end space-x-2 whitespace-nowrap">
                                            <a href="{{ route('admin.articles.edit', $a) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('编辑') }}</a>
                                            <form method="post" action="{{ route('admin.articles.destroy', $a) }}" class="inline" onsubmit="return confirm('{{ __('确定删除？') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">{{ __('删除') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $articles->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
