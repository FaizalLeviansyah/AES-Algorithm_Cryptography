<?php
// Pastikan tidak ada output (HTML, spasi) sebelum tag <?php ini

// Memulai atau melanjutkan sesi yang sudah ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Menghapus semua variabel sesi
$_SESSION = array();

// Menghancurkan cookie sesi jika digunakan
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Menghancurkan sesi di server
session_destroy();

// Mengarahkan ke halaman login utama (index.php di root)
// Pastikan path "index.php" ini benar relatif terhadap lokasi file logout.php ini.
// Jika logout.php ada di root, maka "index.php" sudah benar.
header("Location: index.php");
exit; // Penting untuk menghentikan eksekusi skrip setelah redirect
?>