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

        // Jika validasi berhasil, kode akan lanjut ke sini.
        // Jika gagal, Laravel akan otomatis kembali ke form dengan pesan error.

        // Untuk sementara, kita hentikan dan tampilkan data yang sudah lolos validasi.
        dd($validatedData);
    }

        /**
     * Menampilkan halaman manajemen file.
     */
    public function index()
    {
        // Ambil semua data file dari database
        // Untuk sekarang, kita ambil semua file. Nanti kita sesuaikan berdasarkan role.
        $files = FileModel::latest()->get(); // Mengambil data terbaru di atas

        // Kirim data files ke view
        return view('files.index', [
            'files' => $files,
        ]);
    }
}
