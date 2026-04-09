{{-- AI-GEN-BEGIN --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('提交项目') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-xl">
                    <form method="post" action="{{ route('submissions.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="github_owner" :value="__('GitHub 用户名或组织')" />
                            <x-text-input id="github_owner" name="github_owner" type="text" class="mt-1 block w-full font-mono"
                                :value="old('github_owner')" required autocomplete="off" placeholder="laravel" />
                            <x-input-error class="mt-2" :messages="$errors->get('github_owner')" />
                        </div>

                        <div>
                            <x-input-label for="github_repo" :value="__('仓库名')" />
                            <x-text-input id="github_repo" name="github_repo" type="text" class="mt-1 block w-full font-mono"
                                :value="old('github_repo')" required autocomplete="off" placeholder="framework" />
                            <x-input-error class="mt-2" :messages="$errors->get('github_repo')" />
                        </div>

                        <div>
                            <x-input-label for="pitch" :value="__('推荐语')" />
                            <textarea id="pitch" name="pitch" rows="6" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pitch') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('pitch')" />
                        </div>

                        @if ($errors->has('auto_rules'))
                            <div class="rounded-md bg-red-50 p-3 text-sm text-red-800">
                                <ul class="list-disc ms-4 space-y-1">
                                    @foreach ($errors->get('auto_rules') as $msg)
                                        <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('提交审核') }}</x-primary-button>
                            <a href="{{ route('submissions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('返回列表') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- AI-GEN-END --}}
