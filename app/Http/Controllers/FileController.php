<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- Pastikan ini ada
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
        // dd() adalah fungsi "dump and die" untuk debugging.
        // Ini akan menampilkan semua data yang dikirim dari form dan menghentikan script.
        dd($request->all());
    }
}
