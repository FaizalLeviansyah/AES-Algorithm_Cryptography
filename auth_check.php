<?php
// File: auth_check.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login (berdasarkan session 'username')
if (empty($_SESSION['username'])) {
    // Jika belum login, simpan halaman yang diminta (opsional, untuk redirect kembali setelah login)
    // $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

    // Redirect ke halaman login
    // Asumsi halaman login adalah index.php di root folder
    // Jika proyek Anda ada di subfolder (misal /projekku/), maka path-nya /projekku/index.php
    header('Location: /index.php'); // Sesuaikan path absolut ke halaman login Anda
    exit; // Hentikan eksekusi skrip
}

// Jika pengguna sudah login, Anda bisa memuat variabel global di sini jika mau
// $loggedInUserRole = $_SESSION['role'];
// $loggedInUserFullname = $_SESSION['fullname'];
// $loggedInUserProfilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'path/to/default/image.png';

// Opsional: Perbarui last_activity pengguna
// Pastikan $connect dari config.php tersedia jika Anda ingin melakukan ini di sini.
// Jika config.php belum di-include, Anda bisa include di sini.
/*
if (!isset($connect)) { // Jika $connect belum ada
    require_once __DIR__ . '/config.php'; // Asumsi config.php ada di direktori yang sama
}
if (isset($connect) && isset($_SESSION['username'])) {
    $user_for_activity = $_SESSION['username'];
    $stmt_activity = mysqli_prepare($connect, "UPDATE users SET last_activity=NOW() WHERE username=?");
    if ($stmt_activity) {
        mysqli_stmt_bind_param($stmt_activity, "s", $user_for_activity);
        mysqli_stmt_execute($stmt_activity);
        mysqli_stmt_close($stmt_activity);
    }
}
*/
?>