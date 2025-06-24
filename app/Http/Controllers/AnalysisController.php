<?php

namespace App\Http\Controllers;

use App\Services\EncryptionService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    // Method untuk menampilkan halaman awal analisis
    public function index()
    {
        // Kirim variabel 'results' sebagai array kosong saat pertama kali halaman dimuat
        $results = [];
        return view('analysis.index', compact('results'));
    }

    // Method untuk melakukan analisis dan menampilkan hasilnya
    public function performAnalysis(Request $request)
    {
        // ... (kode validasi dan analisis Anda yang sudah ada tetap sama) ...
        $request->validate([
            'analysis_files.*' => ['required', 'file', 'max:5120'],
            'analysis_files' => ['required', 'array', 'min:1'],
        ]);

        $files = $request->file('analysis_files');
        $password = 'kunci-rahasia-untuk-analisis-yang-sama';
        $results = [];

        foreach ($files as $file) {
            $fileContent = $file->get();
            $fileSize = $file->getSize();

            if ($fileSize > 10 * 1024 * 1024) continue;

            $timeEncrypt128 = $this->measurePerformance($fileContent, $password, '128', 'encrypt');
            $timeDecrypt128 = $this->measurePerformance($fileContent, $password, '128', 'decrypt');
            $timeEncrypt256 = $this->measurePerformance($fileContent, $password, '256', 'encrypt');
            $timeDecrypt256 = $this->measurePerformance($fileContent, $password, '256', 'decrypt');

            $results[] = [
                'fileName' => $file->getClientOriginalName(),
                'fileSize' => $fileSize,
                'aes128' => [ 'encryptionTime' => $timeEncrypt128, 'decryptionTime' => $timeDecrypt128 ],
                'aes256' => [ 'encryptionTime' => $timeEncrypt256, 'decryptionTime' => $timeDecrypt256 ],
            ];
        }

        usort($results, fn($a, $b) => $a['fileSize'] <=> $b['fileSize']);

        return view('analysis.index', compact('results'));
    }

    private function measurePerformance(string $data, string $password, string $bit, string $operation): float
    {
        // ... (kode method measurePerformance Anda tetap sama) ...
        $service = new EncryptionService($password, $bit);

        if ($operation === 'encrypt') {
            $service->setData($data);
            $startTime = microtime(true);
            $service->encrypt();
            $endTime = microtime(true);
        } else {
            $service->setData($data);
            $encryptedData = $service->encrypt();
            $service->setData($encryptedData);
            $startTime = microtime(true);
            $service->decrypt();
            $endTime = microtime(true);
        }
        return ($endTime - $startTime) * 1000;
    }
}
