<?php
session_start();
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idfile   = mysqli_real_escape_string($connect, $_POST['fileid']);
    $password = $_POST['pwdfile'];

    $sql = "SELECT * FROM file WHERE id_file = '$idfile'";
    $result = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if (mysqli_num_rows($result) === 0) {
        echo "<script>alert('File tidak ditemukan.'); window.location.href='dekripsi.php';</script>";
        exit();
    }

    $data = mysqli_fetch_assoc($result);

    $key128   = substr(hash('sha256', $password), 0, 16);
    $key256   = substr(hash('sha256', $password), 0, 32);
    $cipher   = $data['alg_used'] === 'AES-256' ? 'AES-256-ECB' : 'AES-128-ECB';
    $key      = $data['alg_used'] === 'AES-256' ? $key256 : $key128;
    $enc_path = 'hasil_ekripsi/' . $data['file_name_finish'];
    $file_name = $data['file_name_source'];
    $original_hash = $data['hash_check'];

    $encrypted_data = file_get_contents($enc_path);
    $start_time = microtime(true);

    $decrypted = openssl_decrypt($encrypted_data, $cipher, $key, OPENSSL_RAW_DATA);
    $duration_ms = round((microtime(true) - $start_time) * 1000, 3);

    // Check SHA-256 hash
    $current_hash = hash('sha256', $decrypted);
    if ($original_hash !== $current_hash) {
        echo "<script>alert('Password salah atau file rusak.'); window.location.href='dekripsi.php';</script>";
        exit();
    }

    // Save decrypted file
    $save_path = 'hasil_dekripsi/' . $file_name;
    file_put_contents($save_path, $decrypted);

    $update = "UPDATE file SET status = '2', operation_type = 'decrypt' WHERE id_file = '$idfile'";
    mysqli_query($connect, $update);

    echo "<script>alert('File berhasil didekripsi!'); window.location.href='dekripsi.php';</script>";
}
?>