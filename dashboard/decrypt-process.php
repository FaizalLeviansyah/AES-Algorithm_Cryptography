<?php
session_start();
// Diasumsikan config.php dan session.php sudah benar path-nya
// dan session.php menangani otentikasi/pengecekan sesi.
include('../config.php'); 
// include('../session.php'); // Jika session.php hanya start session dan sudah dilakukan di atas, ini bisa jadi duplikat.
// Pastikan session sudah divalidasi (pengguna sudah login) sebelum melanjutkan.
if (empty($_SESSION['username'])) { // Contoh validasi sederhana
    die("Akses tidak sah. Silakan login terlebih dahulu.");
}


if (!isset($_GET['id_file']) || empty($_POST['pwdfile_decrypt'])) {
    // Sebaiknya redirect dengan pesan error ke halaman sebelumnya
    $_SESSION['dekripsi_message'] = "Permintaan tidak valid. ID file atau password tidak ada.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php"); // Redirect ke halaman daftar dekripsi
    exit;
}

$id_file = $_GET['id_file'];
$key_user = $_POST['pwdfile_decrypt'];

// Gunakan prepared statement untuk keamanan
$stmt = mysqli_prepare($connect, "SELECT file_url, alg_used, file_name_source FROM file WHERE id_file = ?");
if (!$stmt) {
    // Sebaiknya redirect dengan pesan error
    die("Gagal menyiapkan query: " . mysqli_error($connect));
}
mysqli_stmt_bind_param($stmt, "s", $id_file);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['dekripsi_message'] = "File tidak ditemukan di database.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php");
    exit;
}

$fullPath = __DIR__ . '/../' . $data['file_url']; // Path ke file terenkripsi (misal: project_root/dashboard/encrypted_files/enc_file.rda)
$alg = $data['alg_used'];
$originalName = $data['file_name_source'];

if (!file_exists($fullPath) || !is_readable($fullPath)) {
    $_SESSION['dekripsi_message'] = "File terenkripsi tidak ditemukan di server atau tidak dapat dibaca.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php?id_file=" . urlencode($id_file)); // Kembali ke form dekripsi file spesifik
    exit;
}

$cipher = $alg === 'AES-128' ? 'aes-128-cbc' : 'aes-256-cbc';
$key_len = $alg === 'AES-128' ? 16 : 32;
$key = substr(hash('sha256', $key_user, true), 0, $key_len);

$base64_data = file_get_contents($fullPath);
$raw_data = base64_decode($base64_data);
$iv_len = openssl_cipher_iv_length($cipher);

if (strlen($raw_data) < $iv_len) {
    $_SESSION['dekripsi_message'] = "Gagal dekripsi: Data file terenkripsi tidak valid (terlalu pendek). Mungkin file rusak atau bukan file terenkripsi yang benar.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: decrypt-file.php?id_file=" . urlencode($id_file));
    exit;
}

$iv = substr($raw_data, 0, $iv_len);
$ciphertext = substr($raw_data, $iv_len);

// === MULAI PENGUKURAN WAKTU DEKRIPSI ===
$start_time_decrypt = microtime(true);

$decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);

// === SELESAI PENGUKURAN WAKTU DEKRIPSI ===
$end_time_decrypt = microtime(true);
$process_time_ms = round(($end_time_decrypt - $start_time_decrypt) * 1000, 4); // Simpan dengan 4 desimal

if ($decrypted === false) {
    $_SESSION['dekripsi_message'] = "Gagal dekripsi. Password salah atau file rusak.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: decrypt-file.php?id_file=" . urlencode($id_file)); // Kembali ke form input password
    exit;
}

$decryptedName = 'dec_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName); // Sanitasi nama file

// --- PERBAIKAN PATH PENYIMPANAN ---
$decryptedResultFolder = __DIR__ . '/decrypted_result/'; // Path: project_root/dashboard/decrypted_result/
if (!is_dir($decryptedResultFolder)) {
    if (!mkdir($decryptedResultFolder, 0755, true)) {
        $_SESSION['dekripsi_message'] = "Gagal membuat direktori penyimpanan hasil dekripsi.";
        $_SESSION['dekripsi_message_type'] = "error";
        header("Location: dekripsi.php");
        exit;
    }
}
$decryptedPathPhysical = $decryptedResultFolder . $decryptedName; // Path fisik lengkap untuk menyimpan file
// --- AKHIR PERBAIKAN PATH PENYIMPANAN ---

if (file_put_contents($decryptedPathPhysical, $decrypted) === false) {
    $_SESSION['dekripsi_message'] = "Gagal menyimpan file hasil dekripsi.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php");
    exit;
}

$fileSizeKB = round(filesize($decryptedPathPhysical) / 1024, 2);
$hash_check_decrypted = hash_file('sha256', $decryptedPathPhysical);

// Path URL untuk database, relatif dari root proyek
$file_url_db_decrypted = 'dashboard/decrypted_result/' . $decryptedName; 

// Update database (gunakan prepared statement)
$stmt_update = mysqli_prepare($connect, "UPDATE file SET 
    status = 2, 
    file_name_finish = ?,
    file_url = ?,
    operation_type = 'dekripsi', /* atau 'dekripsi (sukses)' */
    process_time_ms = ?, /* Gunakan waktu yang sudah dihitung */
    hash_check = ?,
    file_size = ? /* Tambahkan update file_size jika ukurannya berubah setelah dekripsi */
    WHERE id_file = ?
");

if (!$stmt_update) {
    // Sebaiknya redirect dengan pesan error
    die("Gagal menyiapkan statement update: " . mysqli_error($connect));
}

$operation_type_db = 'dekripsi'; // Anda bisa membuatnya lebih spesifik jika mau
mysqli_stmt_bind_param($stmt_update, "ssdsds", 
    $decryptedName, 
    $file_url_db_decrypted, 
    $process_time_ms, 
    $hash_check_decrypted,
    $fileSizeKB,
    $id_file
);

if (mysqli_stmt_execute($stmt_update)) {
    $_SESSION['dekripsi_message'] = "File '" . htmlspecialchars($originalName) . "' berhasil didekripsi.";
    $_SESSION['dekripsi_message_type'] = "success";
} else {
    $_SESSION['dekripsi_message'] = "Gagal mengupdate data file di database: " . mysqli_stmt_error($stmt_update);
    $_SESSION['dekripsi_message_type'] = "error";
    // Hapus file yang sudah didekripsi jika update DB gagal
    if (file_exists($decryptedPathPhysical)) {
        unlink($decryptedPathPhysical);
    }
}
mysqli_stmt_close($stmt_update);

header("Location: dekripsi.php"); // Redirect ke halaman daftar file (yang akan menampilkan pesan)
exit;
?>