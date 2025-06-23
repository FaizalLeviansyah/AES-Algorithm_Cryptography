<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Pastikan use View ini ada

class FileController extends Controller
{
    /**
     * Menampilkan form untuk enkripsi file.
     */
    public function createEncrypt(): View
    {
        return view('files.encrypt');
    }
}
