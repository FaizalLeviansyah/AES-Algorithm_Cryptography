<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total File</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalFiles }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total User</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Divisi</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalDivisions }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Enkripsi</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalEncryptionLogs }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aktivitas Enkripsi Terbaru</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentLogs as $log)
                            <li class="py-3 flex items-center justify-between">
                                <div class="truncate">
                                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $log->user->fullname ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Menenkripsi <span class="font-semibold">{{ $log->file->file_name_source ?? 'N/A' }}</span></p>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($log->encrypted_at)->diffForHumans() }}
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-center text-sm text-gray-500">Belum ada aktivitas.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Komposisi AES</h3>
                    <canvas id="aesPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('aesPieChart');
            const data = @json($aesComposition);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(data).map(bit => `AES-${bit}`),
                    datasets: [{
                        label: 'Jumlah File',
                        data: Object.values(data),
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.7)', // Indigo
                            'rgba(239, 68, 68, 0.7)'  // Red
                        ],
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
