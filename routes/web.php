<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController; //

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Encryption Routes
    Route::get('/enkripsi', [FileController::class, 'createEncrypt'])->name('file.encrypt.create');
    Route::post('/enkripsi', [FileController::class, 'storeEncrypt'])->name('file.encrypt.store');

    // File Management Routes
    Route::get('/files', [FileController::class, 'index'])->name('file.index');
    Route::get('/files/{file}/download-encrypted', [FileController::class, 'downloadEncrypted'])->name('file.download.encrypted');

    // Decryption Routes
    Route::post('/files/{file}/direct-decrypt', [FileController::class, 'directDecrypt'])->name('file.direct_decrypt');
    Route::get('/dekripsi-mandiri', [FileController::class, 'showStandaloneDecryptForm'])->name('file.decrypt.standalone.create');
    Route::post('/dekripsi-mandiri', [FileController::class, 'processStandaloneDecrypt'])->name('file.decrypt.standalone.store');

    // Log Routes
    Route::get('/logs/enkripsi', [FileController::class, 'showEncryptionLogs'])->name('logs.encryption');
    Route::get('/logs/dekripsi', [FileController::class, 'showDecryptionLogs'])->name('logs.decryption');
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('/analisis-aes', [AnalysisController::class, 'index'])->name('analysis.aes')->middleware('can:is-admin');
    Route::post('/analisis-aes', [AnalysisController::class, 'performAnalysis'])->name('analysis.aes.perform')->middleware('can:is-admin');
});

require __DIR__.'/auth.php';
