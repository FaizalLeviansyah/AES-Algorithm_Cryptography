<?php

namespace App\Http\Controllers;

use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\File as FileModel;

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
        FileModel::create([
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

        // 6. Redirect ke halaman dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'File berhasil dienkripsi!');

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

        public function createDecrypt(FileModel $file)
    {
        // Kirim data file yang akan didekripsi ke view
        return view('files.decrypt', [
            'file' => $file,
        ]);
    }

     public function storeDecrypt(Request $request, FileModel $file)
    {
        // 1. Validasi input kunci dari pengguna
        $request->validate([
            'key' => ['required', 'string'],
        ]);

        // 2. Pastikan file terenkripsi ada di server
        if (!Storage::disk('private')->exists($file->file_path)) {
            return back()->with('error', 'File terenkripsi tidak ditemukan di server.');
        }

        // 3. Baca konten file yang terenkripsi
        $encryptedContent = Storage::disk('private')->get($file->file_path);

        // 4. Siapkan service dan coba lakukan dekripsi
        $encryptionService = new EncryptionService($request->key, $file->bit);
        $encryptionService->setData($encryptedContent);
        $decryptedContent = $encryptionService->decrypt();

        // 5. Cek hasil dekripsi. Jika hasilnya 'false', berarti kunci salah.
        if ($decryptedContent === false) {
            // Kembali ke halaman form dengan pesan error
            return back()->with('error', 'Kunci yang Anda masukkan salah!');
        }

        // 6. Jika dekripsi berhasil, kirim file untuk di-download oleh browser
        return response()->streamDownload(function () use ($decryptedContent) {
            echo $decryptedContent;
        }, $file->file_name_source); // Nama file saat di-download akan kembali ke nama aslinya
    }
}
