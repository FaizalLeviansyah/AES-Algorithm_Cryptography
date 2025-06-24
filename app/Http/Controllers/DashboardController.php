<?php

namespace App\Http\Controllers;

use App\Models\File as FileModel;
use App\Models\User;
use App\Models\Division;
use App\Models\EncryptionLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk Kartu Statistik
        $totalFiles = FileModel::count();
        $totalUsers = User::count();
        $totalDivisions = Division::count();
        $totalEncryptionLogs = EncryptionLog::count();

        // Data untuk Pie Chart Komposisi AES
        $aesComposition = FileModel::selectRaw('bit, count(*) as count')
            ->groupBy('bit')
            ->pluck('count', 'bit');

        // Data untuk Log Aktivitas Terbaru
        $recentLogs = EncryptionLog::with('user', 'file')
            ->latest('encrypted_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalFiles',
            'totalUsers',
            'totalDivisions',
            'totalEncryptionLogs',
            'aesComposition',
            'recentLogs'
        ));
    }
}
