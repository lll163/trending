{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新建文章') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-3xl">
                    <form method="post" action="{{ route('admin.articles.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="magazine_issue_id" :value="__('所属期刊（可选）')" />
                            <select id="magazine_issue_id" name="magazine_issue_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">{{ __('无 / 独立文章') }}</option>
                                @foreach ($issues as $iss)
                                    <option value="{{ $iss->id }}" @selected(old('magazine_issue_id') == $iss->id)>
                                        {{ $iss->issue_code }} — {{ $iss->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('magazine_issue_id')" />
                        </div>

                        <div>
                            <x-input-label for="slug" :value="__('slug（URL，小写连字符）')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                                :value="old('slug')" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>

                        <div>
                            <x-input-label for="title" :value="__('标题')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title')" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="body" :value="__('正文（Markdown）')" />
                            <textarea id="body" name="body" rows="18" required
                                class="mt-1 block w-full border-gray-300 font-mono text-sm rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('body') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('body')" />
                        </div>

                        <div>
                            <x-input-label for="sort_order" :value="__('排序（越小越靠前）')" />
                            <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-32"
                                :value="old('sort_order', 0)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('状态')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="draft" @selected(old('status', 'draft') === 'draft')>{{ __('草稿') }}</option>
                                <option value="published" @selected(old('status') === 'published')>{{ __('已发布') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="published_at" :value="__('发布时间（可选）')" />
                            <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full"
                                :value="old('published_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                            <a href="{{ route('admin.articles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
