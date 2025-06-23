<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen File') }}
        </h2>
    </x-slot>

    {{-- Kita gunakan x-data dari Alpine.js untuk mengelola state modal --}}
    <div x-data="{ showModal: false, fileId: null, fileName: '', actionUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar File Terenkripsi</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama File Asli</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ukuran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tgl Upload</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($files as $file)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $file->file_name_source }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ round($file->file_size / 1024, 2) }} KB</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($file->tgl_upload)->format('d M Y, H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                                {{-- Tombol ini sekarang memicu modal --}}
                                                <button @click="showModal = true; fileName = '{{ $file->file_name_source }}'; actionUrl = '{{ route('file.direct_decrypt', $file) }}'" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Dekripsi</button>
                                                <a href="{{ route('file.download.encrypted', $file) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">Download (Terenkripsi)</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                Belum ada file yang dienkripsi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showModal" @keydown.escape.window="showModal = false" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" x-cloak>
            <div @click.away="showModal = false" class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Dekripsi File</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="'Masukkan kunci untuk file: ' + fileName"></p>
                    </div>
                    <form :action="actionUrl" method="POST" class="px-4 py-3">
                        @csrf
                        <x-input-label for="key_modal" :value="__('Kunci Dekripsi (Password)')" class="sr-only" />
                        <x-text-input id="key_modal" class="block mt-1 w-full" type="password" name="key" required placeholder="Masukkan kunci..." />

                        <div class="items-center px-4 py-3">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Dekripsi & Download
                            </button>
                        </div>
                    </form>
                    <button @click="showModal = false" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mt-2">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
