{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新建标签') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-xl">
                    <form method="post" action="{{ route('admin.tags.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="slug" :value="__('slug（小写、连字符）')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                                :value="old('slug')" required autocomplete="off" placeholder="machine-learning" />
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>

                        <div>
                            <x-input-label for="title" :value="__('显示标题')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title')" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                            <a href="{{ route('admin.tags.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
