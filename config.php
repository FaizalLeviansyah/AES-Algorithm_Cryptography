<?php
// File: config.php
$connect = mysqli_connect("localhost", "root", "", "aes_orig");

// Check connection
if (!$connect) {
    // Hentikan eksekusi dan tampilkan pesan error yang lebih informatif saat pengembangan
    // Untuk produksi, Anda mungkin ingin mencatat error ini ke file log daripada menampilkannya ke pengguna.
    die("Koneksi database gagal: " . mysqli_connect_error() . " (Error No: " . mysqli_connect_errno() . ")");
}

// Opsional: Set karakter set ke UTF-8 untuk mendukung berbagai karakter
mysqli_set_charset($connect, "utf8mb4");
?>