<?php

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

    // ▼▼▼ TAMBAHKAN ROUTE BARU INI ▼▼▼
    Route::get('/files', [FileController::class, 'index'])->name('file.index');

     // Route untuk Dekripsi
    Route::get('/files/{file}/dekripsi', [FileController::class, 'createDecrypt'])->name('file.decrypt.create');
    // Route untuk memproses data dari form dekripsi
    Route::post('/files/{file}/dekripsi', [FileController::class, 'storeDecrypt'])->name('file.decrypt.store');
    Route::get('/files/{file}/download-encrypted', [FileController::class, 'downloadEncrypted'])->name('file.download.encrypted');
    Route::get('/dekripsi/berhasil', [FileController::class, 'decryptSuccess'])->name('file.decrypt.success');
});

require __DIR__.'/auth.php';
