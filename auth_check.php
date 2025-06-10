<?php
// File: auth_check.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi database tersedia
if (!isset($connect)) {
    require_once __DIR__ . '/config.php'; // pastikan path ini benar
}

// Cek login
if (empty($_SESSION['username'])) {
    header('Location: /index.php'); // arahkan ke login
    exit;
}

// (Opsional) Cek role, bisa digunakan untuk proteksi halaman admin-only
// if ($_SESSION['role'] !== 'admin') {
//     die("Akses ditolak");
// }
?>
