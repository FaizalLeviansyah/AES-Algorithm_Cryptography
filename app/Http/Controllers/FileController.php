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
        return view('files.encrypt'); // <-- Ini akan memanggil file view
    }
}
