<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dekripsi File') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        File: <span class="font-normal">{{ $file->file_name_source }}</span>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Masukkan kunci yang benar untuk mendekripsi dan mengunduh file ini.
                    </p>

                    {{-- FORM DEKRIPSI --}}
                    <form method="POST" action="{{ route('file.decrypt.store', $file) }}">
                        @csrf

                        <div>
                            <x-input-label for="key" :value="__('Kunci Dekripsi (Password)')" />
                            <x-text-input id="key" class="block mt-1 w-full" type="password" name="key" required />
                            <x-input-error :messages="$errors->get('key')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Dekripsi & Download') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
