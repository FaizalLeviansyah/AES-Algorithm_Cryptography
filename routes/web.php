<?php
Route::get('/clear-session', function() {
    session()->flush();
    return 'Session berhasil dibersihkan! Silakan kembali ke halaman login.';
});

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController; //

Route::get('/', function () {
    return view('welcome');
});

// ▼▼▼ TAMBAHKAN ROUTE INI UNTUK DEBUGGING ▼▼▼
Route::get('/cek-konfigurasi', function () {
    dd([
        'Database Connection' => config('database.default'),
        'Session Driver' => config('session.driver'),
        'Database Name Used' => config('database.connections.mysql.database'),
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk Enkripsi
    Route::get('/enkripsi', [FileController::class, 'createEncrypt'])->name('file.encrypt.create');
    Route::post('/enkripsi', [FileController::class, 'storeEncrypt'])->name('file.encrypt.store');

    // Route untuk Manajemen File
    Route::get('/files', [FileController::class, 'index'])->name('file.index');

    // ▼▼▼ TAMBAHKAN KEMBALI ROUTE INI ▼▼▼
    Route::get('/files/{file}/download-encrypted', [FileController::class, 'downloadEncrypted'])->name('file.download.encrypted');

    // Route untuk Dekripsi via Modal
    Route::post('/files/{file}/direct-decrypt', [FileController::class, 'directDecrypt'])->name('file.direct_decrypt');

    Route::get('/logs/enkripsi', [FileController::class, 'showEncryptionLogs'])->name('logs.encryption');
    Route::get('/logs/dekripsi', [FileController::class, 'showDecryptionLogs'])->name('logs.decryption');


});

require __DIR__.'/auth.php';
