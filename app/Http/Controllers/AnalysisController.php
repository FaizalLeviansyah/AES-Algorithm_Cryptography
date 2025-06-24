<?php

namespace App\Http\Controllers;

use App\Services\EncryptionService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index()
    {
        // Data sampel untuk diuji
        $sampleData = str_repeat("Ini adalah contoh teks untuk pengujian kinerja enkripsi AES. ", 5000); // Membuat data sekitar 400KB
        $password = 'kunci-rahasia-untuk-analisis';

        // Tes AES-128
        $startTime128 = microtime(true);
        $service128 = new EncryptionService($password, '128');
        $service128->setData($sampleData);
        $encrypted128 = $service128->encrypt();
        $endTime128 = microtime(true);
        $time128 = ($endTime128 - $startTime128) * 1000; // dalam milidetik

        // Tes AES-256
        $startTime256 = microtime(true);
        $service256 = new EncryptionService($password, '256');
        $service256->setData($sampleData);
        $encrypted256 = $service256->encrypt();
        $endTime256 = microtime(true);
        $time256 = ($endTime256 - $startTime256) * 1000; // dalam milidetik

        return view('analysis.index', [
            'dataSize' => strlen($sampleData),
            'time128' => $time128,
            'time256' => $time256,
        ]);
    }
}
