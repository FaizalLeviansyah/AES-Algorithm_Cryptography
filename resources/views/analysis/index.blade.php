<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Analisis Performa AES') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Hasil Perbandingan Enkripsi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Pengujian dilakukan pada data sampel berukuran: **{{ round($dataSize / 1024, 2) }} KB**.
                        <br>
                        <em class="text-xs">*Hasil dapat bervariasi tergantung beban server saat ini. Refresh halaman untuk pengujian ulang.</em>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
                            <h4 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">AES-128</h4>
                            <p class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($time128, 2) }} <span class="text-lg font-medium">ms</span></p>
                            <p class="text-sm text-gray-500">Waktu Proses Enkripsi</p>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
                            <h4 class="text-2xl font-bold text-red-600 dark:text-red-400">AES-256</h4>
                            <p class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($time256, 2) }} <span class="text-lg font-medium">ms</span></p>
                            <p class="text-sm text-gray-500">Waktu Proses Enkripsi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
