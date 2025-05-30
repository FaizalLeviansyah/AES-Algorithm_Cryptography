<?php
// File: proses_login.php

// Selalu mulai sesi di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php'; // Memanggil konfigurasi database

// Hanya proses jika ada data POST dari form login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password_input = trim($_POST['password']); // Password dari form

    // PERINGATAN KEAMANAN: MD5 sudah tidak aman!
    // Anda SANGAT DISARANKAN untuk beralih ke password_hash() dan password_verify().
    // Untuk saat ini, agar sesuai dengan database Anda yang masih MD5:
    $hashed_password_md5 = md5($password_input);

    // GUNAKAN PREPARED STATEMENTS untuk mencegah SQL Injection!
    $query_sql = "SELECT id, username, password, fullname, role, profile_pic FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($connect, $query_sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password_md5);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        if ($data) {
            // Login berhasil
            $_SESSION['user_id'] = $data['id']; // Simpan ID user jika perlu
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['fullname'] = $data['fullname'];
            if (isset($data['profile_pic'])) { // Jika ada kolom profile_pic
                $_SESSION['profile_pic'] = $data['profile_pic'];
            }

            // Regenerasi ID sesi setelah login untuk keamanan tambahan
            session_regenerate_id(true);

            header('Location: dashboard/'); // Arahkan ke folder dashboard
            exit; // Selalu exit setelah redirect header
        } else {
            // Login gagal (username atau password salah)
            $_SESSION['login_error'] = "Username atau Password salah!";
            header('Location: index.php'); // Kembali ke halaman login (index.php di root)
            exit;
        }
    } else {
        // Error pada saat mempersiapkan statement SQL
        error_log("MySQLi prepare failed: " . mysqli_error($connect)); // Catat error ke log server
        $_SESSION['login_error'] = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
        header('Location: index.php'); // Kembali ke halaman login
        exit;
    }
} else {
    // Jika file ini diakses langsung tanpa metode POST 'login',
    // redirect ke halaman login.
    header('Location: index.php');
    exit;
}
?>