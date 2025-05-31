<?php
session_start();
include('../config.php');
include('../session.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $originalFile = $_FILES['file'];
    $password = $_POST['pwdfile'];
    $desc = $_POST['desc'];
    $algorithm = $_POST['algorithm']; // AES-128 / AES-256

    if (empty($password) || empty($originalFile['name']) || empty($algorithm)) {
        die("Data tidak lengkap.");
    }

    $originalName = basename($originalFile['name']);
    $raw_content = file_get_contents($originalFile['tmp_name']);

    $cipher = $algorithm === 'AES-128' ? 'aes-128-cbc' : 'aes-256-cbc';
    $key_length = $algorithm === 'AES-128' ? 16 : 32;
    $key = substr(hash('sha256', $password, true), 0, $key_length);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

    $ciphertext = openssl_encrypt($raw_content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $final_data = base64_encode($iv . $ciphertext);

    $newFileName = 'enc_' . time() . '_' . $originalName . '.rda';
    $destinationFolder = __DIR__ . '/encrypted_result/';
    if (!is_dir($destinationFolder)) {
        mkdir($destinationFolder, 0755, true);
    }
    
    $destinationPath = $destinationFolder . $newFileName;
    file_put_contents($destinationPath, $final_data);

    $duration = 0; // dapat diukur juga
    $fileSizeKB = round(filesize($destinationPath) / 1024, 2);
    $hash = hash_file('sha256', $destinationPath);

    $db_path = 'dashboard/encrypted_result/' . $newFileName;
    $query = "INSERT INTO file (
        username, file_name_source, file_name_finish, file_url, 
        file_size, password, alg_used, process_time_ms, 
        operation_type, hash_check, tgl_upload, status, keterangan
    ) VALUES (
        '$username', '$originalName', '$newFileName', '$db_path',
        '$fileSizeKB', 'KEY_NOT_STORED', '$algorithm', '$duration',
        'encrypt', '$hash', NOW(), 1, '$desc'
    )";

    if (mysqli_query($connect, $query)) {
        header("Location: enkripsi.php?success=1");
    } else {
        die("Gagal menyimpan data: " . mysqli_error($connect));
    }
}