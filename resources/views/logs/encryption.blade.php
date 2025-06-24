<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Log Riwayat Enkripsi') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar Aktivitas Enkripsi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama File</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Di-enkripsi oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $log->file->file_name_source ?? 'File Dihapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->user->username ?? 'User Dihapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($log->encrypted_at)->format('d M Y, H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-4 text-center">Belum ada aktivitas enkripsi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
