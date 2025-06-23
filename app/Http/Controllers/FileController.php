<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
