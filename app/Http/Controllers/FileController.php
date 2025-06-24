<?php

namespace App\Http\Controllers;

use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\File as FileModel;
use App\Models\DecryptionLog;
use App\Models\EncryptionLog;


class FileController extends Controller
{
    /**
     * Menampilkan form untuk enkripsi file.
     */
    public function createEncrypt(): View
    {
        return view('files.encrypt');
    }

    /**
     * Meng-handle proses upload dan enkripsi file.
     */
    public function storeEncrypt(Request $request)
    {
        // 1. Validasi Input dari Form
        $validatedData = $request->validate([
            'file' => [
                'required', // File wajib ada
                'file',     // Harus berupa file
                'mimes:pdf,doc,docx,xls,xlsx', // Hanya tipe file ini yang diizinkan
                'max:5120', // Ukuran file maksimal 5MB (5120 KB), bisa disesuaikan
            ],
            'key' => ['required', 'string', 'min:8'], // Kunci wajib ada, minimal 8 karakter
            'description' => ['nullable', 'string', 'max:255'], // Keterangan boleh kosong
            'bit' => ['required', 'in:128,256'], // Ukuran bit wajib ada, dan nilainya harus 128 atau 256
        ]);

                // 2. Dapatkan file asli dan kontennya
        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $fileContent = $file->get();

        // 3. Lakukan Enkripsi menggunakan Service
        $encryptionService = new EncryptionService($validatedData['key'], $validatedData['bit']);
        $encryptionService->setData($fileContent);
        $encryptedContent = $encryptionService->encrypt();

        // 4. Simpan file yang sudah terenkripsi
        $encryptedFileName = time() . '-' . str_replace(' ', '_', $originalFileName) . '.enc';
        Storage::disk('private')->put($encryptedFileName, $encryptedContent);

        // 5. Simpan informasi file ke database
        // 5. Simpan informasi file ke database
        $newlyCreatedFile = FileModel::create([
            'file_name_source' => $originalFileName,
            'file_name_finish' => $encryptedFileName,
            'file_path' => $encryptedFileName,
            'file_size' => $file->getSize(),
            'password' => $validatedData['description'],
            'tgl_upload' => now(),
            'username' => Auth::user()->username,
            'status' => '1',
            'bit' => $validatedData['bit'],
        ]);

        // 6. Catat aktivitas enkripsi ke dalam log
        EncryptionLog::create([
            'file_id' => $newlyCreatedFile->id_file,
            'user_id' => Auth::id(),
            'encrypted_at' => $newlyCreatedFile->tgl_upload,
        ]);
        // 7. Redirect ke halaman dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'File berhasil dienkripsi dan log telah dicatat!');

        // Jika validasi berhasil, kode akan lanjut ke sini.
        // Jika gagal, Laravel akan otomatis kembali ke form dengan pesan error.
    }

        /**
     * Menampilkan halaman manajemen file.
     */
    public function index()
    {
        // Ambil semua data file dari database, urutkan berdasarkan tgl_upload
        $files = FileModel::orderBy('tgl_upload', 'desc')->get();

        // Kirim data files ke view
        return view('files.index', [
            'files' => $files,
        ]);
    }

        public function downloadEncrypted(FileModel $file)
    {
        // Pastikan file ada di storage
        if (!Storage::disk('private')->exists($file->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        // Gunakan Storage::download untuk langsung mengirim file dari storage private
        return Storage::disk('private')->download($file->file_path, $file->file_name_finish);
    }

     public function directDecrypt(Request $request, FileModel $file)
    {
        // 1. Validasi input kunci
        $request->validate(['key' => ['required', 'string']]);

        // 2. Cek apakah file ada di storage
        if (!Storage::disk('private')->exists($file->file_path)) {
            return back()->with('error', 'File terenkripsi tidak ditemukan di server.');
        }

        // 3. Baca konten terenkripsi
        $encryptedContent = Storage::disk('private')->get($file->file_path);

        // 4. Siapkan service dan coba dekripsi
        $encryptionService = new EncryptionService($request->key, $file->bit);
        $encryptionService->setData($encryptedContent);
        $decryptedContent = $encryptionService->decrypt();

        // 5. Jika dekripsi gagal (kunci salah), kembali dengan pesan error
        if ($decryptedContent === false) {
            return back()->with('error', 'Kunci yang Anda masukkan untuk file "' . $file->file_name_source . '" salah!');
        }

                // ▼▼▼ TAMBAHKAN BLOK INI ▼▼▼
        // Catat aktivitas dekripsi ke dalam log
        DecryptionLog::create([
            'file_id' => $file->id_file,
            'user_id' => Auth::id(),
            'decrypted_at' => now(),
        ]);
        // ▲▲▲ SELESAI ▲▲▲

        // (Nanti kita akan tambahkan pencatatan log di sini)

        // 6. Jika berhasil, langsung kirim file untuk di-download
        return response()->streamDownload(function () use ($decryptedContent) {
            echo $decryptedContent;
        }, $file->file_name_source);
    }

        public function showEncryptionLogs()
    {
        $logs = EncryptionLog::with(['file', 'user'])->latest('encrypted_at')->get();
        return view('logs.encryption', ['logs' => $logs]);
    }

    public function showDecryptionLogs()
    {
        $logs = DecryptionLog::with(['file', 'user'])->latest('decrypted_at')->get();
        return view('logs.decryption', ['logs' => $logs]);
    }

    public function showStandaloneDecryptForm()
{
    return view('files.standalone-decrypt');
}

/**
 * Memproses file terenkripsi yang di-upload untuk didekripsi.
 */
        public function processStandaloneDecrypt(Request $request)
    {
        // 1. Validasi input: harus ada file dan kunci
        $validatedData = $request->validate([
            'encrypted_file' => ['required', 'file'],
            'key' => ['required', 'string'],
        ]);

        $encryptedFileObject = $request->file('encrypted_file');
        $encryptedContent = $encryptedFileObject->get();
        $key = $validatedData['key'];

        // 2. Coba dekripsi dengan AES-256 terlebih dahulu
        $encryptionService256 = new EncryptionService($key, '256');
        $encryptionService256->setData($encryptedContent);
        $decryptedContent = $encryptionService256->decrypt();

        // 3. Jika gagal, coba dengan AES-128
        if ($decryptedContent === false) {
            $encryptionService128 = new EncryptionService($key, '128');
            $encryptionService128->setData($encryptedContent);
            $decryptedContent = $encryptionService128->decrypt();
        }

        // 4. Jika keduanya gagal, berarti kunci atau file salah
        if ($decryptedContent === false) {
            return back()->with('error', 'Dekripsi gagal! Pastikan file dan kunci yang Anda masukkan benar.');
        }

        // ▼▼▼ BAGIAN YANG DIPERBAIKI ▼▼▼

        // 5. Dapatkan nama file yang di-upload oleh pengguna
        $uploadedFileName = $encryptedFileObject->getClientOriginalName();

        // 6. Coba hapus ekstensi '.enc' untuk mendapatkan nama file asli
        // Ini dengan asumsi file yang di-upload selalu berakhiran .enc
        if (str_ends_with(strtolower($uploadedFileName), '.enc')) {
            $originalFileName = substr($uploadedFileName, 0, -4);
        } else {
            // Jika tidak ada .enc, gunakan nama file yang di-upload apa adanya
            $originalFileName = $uploadedFileName;
        }

        // 7. Jika berhasil, kirim file untuk di-download dengan nama yang sudah diperbaiki
        return response()->streamDownload(function () use ($decryptedContent) {
            echo $decryptedContent;
        }, $originalFileName);
    }
}
