<?php
// FILE: dashboard/encrypt-process.php (VERSI FINAL - AMAN & SESUAI DB)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['username'])) {
    $_SESSION['encrypt_message'] = "Sesi tidak valid. Silakan login ulang.";
    $_SESSION['encrypt_message_type'] = "error";
    header('Location: enkripsi.php');
    exit;
}

if (!isset($connect)) {
    die("Koneksi database gagal: variabel '$connect' tidak ditemukan.");
}

define('PBKDF2_HASH_ALGORITHM', 'sha256');
define('PBKDF2_ITERATIONS', 10000); 
define('SALT_BYTE_SIZE', 16);      
define('IV_BYTE_SIZE', 16);        

// Pastikan nama tombol submit dari form adalah 'encrypt_now_button'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encrypt_now_button'])) {

    $username = $_SESSION['username'];
    $originalFile = $_FILES['file'];
    $password = $_POST['pwdfile'];
    $desc = trim($_POST['desc']);
    $algorithm_choice = $_POST['algorithm']; // Menerima 'AES-128-CBC' atau 'AES-256-CBC'

    // Validasi input
    if (empty($password) || !isset($originalFile) || $originalFile['error'] !== UPLOAD_ERR_OK || empty($algorithm_choice)) {
        $_SESSION['encrypt_message'] = "Data tidak lengkap atau terjadi kesalahan saat mengunggah file.";
        $_SESSION['encrypt_message_type'] = "error";
        header('Location: enkripsi.php');
        exit;
    }

    $originalName = basename($originalFile['name']);
    $raw_content = file_get_contents($originalFile['tmp_name']);
    
    // Tentukan parameter enkripsi
    if ($algorithm_choice === 'AES-128-CBC') {
        $cipher = 'aes-128-cbc';
        $key_length = 16;
    } elseif ($algorithm_choice === 'AES-256-CBC') {
        $cipher = 'aes-256-cbc';
        $key_length = 32;
    } else {
        $_SESSION['encrypt_message'] = "Algoritma enkripsi tidak valid.";
        $_SESSION['encrypt_message_type'] = "error";
        header('Location: enkripsi.php');
        exit;
    }
    
    // Proses Kriptografi yang Aman
    $salt_raw = openssl_random_pseudo_bytes(SALT_BYTE_SIZE);
    $key = hash_pbkdf2(PBKDF2_HASH_ALGORITHM, $password, $salt_raw, PBKDF2_ITERATIONS, $key_length, true);
    $iv_raw = openssl_random_pseudo_bytes(IV_BYTE_SIZE);

    $start_time = microtime(true);
    $ciphertext = openssl_encrypt($raw_content, $cipher, $key, OPENSSL_RAW_DATA, $iv_raw);
    $duration = round((microtime(true) - $start_time) * 1000, 4);

    if ($ciphertext === false) {
        die("Enkripsi gagal: " . openssl_error_string());
    }

    // Hanya simpan ciphertext murni di file
    $enc_filename = 'enc_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName) . '.enc';
    $destinationFolder = __DIR__ . '/encrypted_result/'; // Menggunakan nama folder Anda
    if (!is_dir($destinationFolder)) {
        mkdir($destinationFolder, 0755, true);
    }
    $destinationPath = $destinationFolder . $enc_filename;

    if (file_put_contents($destinationPath, $ciphertext) === false) {
        die("Gagal menyimpan file terenkripsi.");
    }
    
    // Siapkan data untuk database
    $hash = hash_file('sha256', $destinationPath);
    $size_kb = round(filesize($destinationPath) / 1024, 2);
    $now = date('Y-m-d H:i:s');
    $salt_hex = bin2hex($salt_raw);
    $iv_hex = bin2hex($iv_raw);
    $kdf_iterations = PBKDF2_ITERATIONS;
    $db_path = 'dashboard/encrypted_result/' . $enc_filename;

    // PERBAIKAN: Query INSERT disesuaikan dengan tabel yang sudah bersih (tanpa key_derivation, salt_hex, iv_hex, password)
    $stmt = $connect->prepare("INSERT INTO file (
        username, file_name_source, file_name_finish, file_url, file_size_kb, 
        alg_used, process_time_ms, operation_type, hash_check, status, 
        keterangan, password_salt_hex, file_iv_hex, kdf_iterations, 
        tgl_upload, tgl_encrypt
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare gagal: " . $connect->error);
    }
    
    $op = 'encrypt';
    $st = '1';
    // PERBAIKAN: bind_param disesuaikan dengan 16 placeholder
    $stmt->bind_param("ssssdsdssisssiss",
        $username,
        $originalName,
        $enc_filename,
        $db_path,
        $size_kb,
        $algorithm_choice,
        $duration,
        $op,
        $hash,
        $st,
        $desc,
        $salt_hex,
        $iv_hex,
        $kdf_iterations,
        $now, // untuk tgl_upload
        $now  // untuk tgl_encrypt
    );

    if ($stmt->execute()) {
        $_SESSION['dekripsi_message'] = "File '" . htmlspecialchars($originalName) . "' berhasil dienkripsi!";
        $_SESSION['dekripsi_message_type'] = "success";
        header("Location: dekripsi.php");
        exit();
    } else {
        die("Execute gagal: " . $stmt->error);
    }

    $stmt->close();
    $connect->close();
}
?>