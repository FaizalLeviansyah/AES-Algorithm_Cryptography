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

    // ▼▼▼ BARIS INI YANG HILANG DARI KODE ANDA ▼▼▼
    Route::get('/enkripsi', [FileController::class, 'createEncrypt'])->name('file.encrypt.create');

    // ▼▼▼ TAMBAHKAN ROUTE BARU INI ▼▼▼
    // Route untuk memproses data dari form enkripsi
    Route::post('/enkripsi', [FileController::class, 'storeEncrypt'])->name('file.encrypt.store');
});

require __DIR__.'/auth.php';
