<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dekripsi File Mandiri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Halaman ini digunakan untuk mendekripsi file terenkripsi (.enc/.rda) yang Anda terima dari luar sistem.
                    </p>

                    <form method="POST" action="{{ route('file.decrypt.standalone.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mt-4">
                            <x-input-label for="encrypted_file" :value="__('Pilih File Terenkripsi (.enc)')" />
                            <input id="encrypted_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="encrypted_file" required>
                            <x-input-error :messages="$errors->get('encrypted_file')" class="mt-2" />
                        </div>

                        <div class="mt-4">
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
