{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新建月刊期号') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-xl">
                    <form method="post" action="{{ route('admin.magazine-issues.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="issue_code" :value="__('期号标识（小写、连字符，如 2026-04）')" />
                            <x-text-input id="issue_code" name="issue_code" type="text" class="mt-1 block w-full font-mono"
                                :value="old('issue_code')" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('issue_code')" />
                        </div>

                        <div>
                            <x-input-label for="title" :value="__('标题')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title')" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="cover" :value="__('封面图（可选，将保存至 storage/app/public）')" />
                            <input id="cover" name="cover" type="file" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-600" />
                            <x-input-error class="mt-2" :messages="$errors->get('cover')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('状态')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" @selected(old('status', 'draft') === 'draft')>{{ __('草稿') }}</option>
                                <option value="published" @selected(old('status') === 'published')>{{ __('已发布') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="published_at" :value="__('发布时间（可选；留空且状态为已发布则使用当前时间）')" />
                            <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full"
                                :value="old('published_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                            <a href="{{ route('admin.magazine-issues.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
