<?php
// FILE: dashboard/decrypt-process.php (REVISI TOTAL)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config.php';

// Validasi sesi dan input dasar
if (empty($_SESSION['username'])) {
    die("Akses tidak sah. Silakan login terlebih dahulu.");
}

if (!isset($_GET['id_file']) || empty($_POST['pwdfile_decrypt'])) {
    $_SESSION['dekripsi_message'] = "Permintaan tidak valid. ID file atau password tidak disediakan.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php");
    exit;
}

$id_file = (int)$_GET['id_file'];
$password = $_POST['pwdfile_decrypt'];
$current_user = $_SESSION['username'];
$current_role = $_SESSION['role'];

// 1. Ambil data file dari database dengan aman
$query = "SELECT file_url, alg_used, file_name_source, password_salt_hex, file_iv_hex, kdf_iterations, username FROM file WHERE id_file = ? AND status = '1'";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $id_file);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['dekripsi_message'] = "File tidak ditemukan atau sudah dalam status terdekripsi.";
    $_SESSION['dekripsi_message_type'] = "warning";
    header("Location: dekripsi.php");
    exit;
}

$file_data = $result->fetch_assoc();
$stmt->close();

// Keamanan: Pastikan hanya admin atau pemilik file yang bisa mendekripsi
if ($current_role !== 'superadmin' && $current_role !== 'admin' && $file_data['username'] !== $current_user) {
    $_SESSION['dekripsi_message'] = "Anda tidak memiliki izin untuk mengakses file ini.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php");
    exit;
}

// 2. Persiapan parameter kriptografi
$file_path_encrypted_physical = __DIR__ . '/../' . $file_data['file_url'];

if (!file_exists($file_path_encrypted_physical) || !is_readable($file_path_encrypted_physical)) {
    $_SESSION['dekripsi_message'] = "Gagal dekripsi: File sumber terenkripsi tidak ditemukan di server.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: decrypt-file.php?id_file=" . urlencode($id_file));
    exit;
}

$cipher = strtolower($file_data['alg_used']); // 'aes-128-cbc' atau 'aes-256-cbc'
$key_length = ($cipher === 'aes-128-cbc') ? 16 : 32;
$salt = hex2bin($file_data['password_salt_hex']);
$iv = hex2bin($file_data['file_iv_hex']);
$iterations = (int)$file_data['kdf_iterations'];

// 3. Proses Dekripsi Sebenarnya
$encrypted_content = file_get_contents($file_path_encrypted_physical);
$derived_key = hash_pbkdf2('sha256', $password, $salt, $iterations, $key_length, true);

$start_time = microtime(true);
$decrypted_content = openssl_decrypt($encrypted_content, $cipher, $derived_key, OPENSSL_RAW_DATA, $iv);
$duration_ms = round((microtime(true) - $start_time) * 1000, 4);

if ($decrypted_content === false) {
    $_SESSION['dekripsi_message'] = "Dekripsi gagal. Kemungkinan besar password salah atau file rusak.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: decrypt-file.php?id_file=" . urlencode($id_file));
    exit;
}

// 4. Simpan hasil dekripsi
$original_name = $file_data['file_name_source'];
$decrypted_filename = 'dec_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $original_name);
$output_dir = __DIR__ . '/decrypted_result/'; // Folder tujuan: dashboard/hasil_dekripsi/
$output_path_physical = $output_dir . $decrypted_filename;

if (!is_dir($output_dir)) {
    if (!mkdir($output_dir, 0755, true)) {
        die("Fatal Error: Gagal membuat direktori 'hasil_dekripsi'. Pastikan folder 'dashboard' memiliki izin tulis.");
    }
}

if (file_put_contents($output_path_physical, $decrypted_content) === false) {
    $_SESSION['dekripsi_message'] = "Gagal menyimpan file hasil dekripsi ke server.";
    $_SESSION['dekripsi_message_type'] = "error";
    header("Location: dekripsi.php");
    exit;
}

// 5. Update Database dengan Lengkap (TERMASUK tgl_decrypt)
$now = date('Y-m-d H:i:s');
$db_path_decrypted = 'dashboard/decrypted_result/' . $decrypted_filename;
$size_kb_decrypted = round(filesize($output_path_physical) / 1024, 2);
$hash_decrypted = hash_file('sha256', $output_path_physical);
$new_status = '2'; // Status 2 = Terdekripsi
$new_operation_type = 'dekripsi';

$update_query = "UPDATE file SET 
                    status = ?, 
                    tgl_decrypt = ?, 
                    file_name_finish = ?, 
                    file_url = ?,
                    file_size_kb = ?,
                    hash_check = ?,
                    operation_type = ?,
                    process_time_ms = ?
                WHERE id_file = ?";

$update_stmt = $connect->prepare($update_query);
$update_stmt->bind_param("ssssdsidi",
    $new_status,
    $now, // Mengisi tgl_decrypt
    $decrypted_filename,
    $db_path_decrypted,
    $size_kb_decrypted,
    $hash_decrypted,
    $new_operation_type,
    $duration_ms,
    $id_file
);

if ($update_stmt->execute()) {
    $_SESSION['dekripsi_message'] = "File '" . htmlspecialchars($original_name) . "' berhasil didekripsi.";
    $_SESSION['dekripsi_message_type'] = "success";
} else {
    $_SESSION['dekripsi_message'] = "Gagal mengupdate status file di database: " . $update_stmt->error;
    $_SESSION['dekripsi_message_type'] = "error";
    // Cleanup: hapus file yang gagal diupdate ke db
    if (file_exists($output_path_physical)) {
        unlink($output_path_physical);
    }
}
$update_stmt->close();

header("Location: dekripsi.php");
exit;
?>