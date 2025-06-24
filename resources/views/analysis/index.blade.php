<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Analisis Performa AES Komprehensif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- FORM UPLOAD --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">1. Uji Kinerja Enkripsi & Dekripsi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Pilih **satu atau beberapa file** sekaligus (maks. 5MB per file) untuk dianalisis. Sistem akan membandingkan performa AES-128 dan AES-256 untuk setiap file.
                    </p>
                    <form action="{{ route('analysis.aes.perform') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            {{-- Ubah input untuk menerima multiple file --}}
                            <input id="analysis_files" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" type="file" name="analysis_files[]" required multiple>
                            <x-input-error :messages="$errors->get('analysis_files.*')" class="mt-2" />
                        </div>
                        <div class="flex items-center mt-4">
                            <x-primary-button>
                                {{ __('Jalankan Analisis') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- HASIL ANALISIS --}}
            @if(isset($results) && count($results) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">2. Tabel Hasil Uji Kinerja</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama File</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ukuran (KB)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enkripsi AES-128 (ms)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enkripsi AES-256 (ms)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($results as $result)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $result['fileName'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($result['fileSize'] / 1024, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($result['aes128']['encryptionTime'], 4) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($result['aes256']['encryptionTime'], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">3. Grafik Visualisasi Performa Enkripsi</h3>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(isset($results) && count($results) > 0)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('performanceChart');
                const results = @json($results);

                // Siapkan data untuk grafik
                // Labels untuk sumbu X adalah ukuran file dalam KB
                const labels = results.map(r => (r.fileSize / 1024).toFixed(2) + ' KB');

                // Data untuk setiap garis pada sumbu Y
                const enc128Data = results.map(r => r.aes128.encryptionTime);
                const dec128Data = results.map(r => r.aes128.decryptionTime);
                const enc256Data = results.map(r => r.aes256.encryptionTime);
                const dec256Data = results.map(r => r.aes256.decryptionTime);

                new Chart(ctx, {
                    type: 'bar', // <-- Mengubah tipe chart menjadi 'line'
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Enkripsi AES-128 (ms)',
                                data: enc128Data,
                                borderColor: 'rgba(79, 70, 229, 1)',
                                backgroundColor: 'rgba(79, 70, 229, 0.7)', // <--- UBAH DI SINI
                                tension: 0.1,
                                fill: false,
                            },
                            {
                                label: 'Dekripsi AES-128 (ms)',
                                data: dec128Data,
                                borderColor: 'rgba(5, 150, 105, 1)',
                                backgroundColor: 'rgba(5, 150, 105, 0.7)', // <--- UBAH DI SINI
                                tension: 0.1,
                                fill: false,
                            },
                            {
                                label: 'Enkripsi AES-256 (ms)',
                                data: enc256Data,
                                borderColor: 'rgba(217, 119, 6, 1)',
                                backgroundColor: 'rgba(217, 119, 6, 0.7)', // <--- UBAH DI SINI
                                tension: 0.1,
                                fill: false,
                            },
                            {
                                label: 'Dekripsi AES-256 (ms)',
                                data: dec256Data,
                                borderColor: 'rgba(220, 38, 38, 1)',
                                backgroundColor: 'rgba(220, 38, 38, 0.7)', // <--- UBAH DI SINI
                                tension: 0.1,
                                fill: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Perbandingan Waktu Proses vs. Ukuran File' }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Ukuran File'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Waktu (milidetik)'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
        @endpush
    @endif
</x-app-layout>
