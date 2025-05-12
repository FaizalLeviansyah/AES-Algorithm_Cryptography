<?php
session_start();
include "../config.php";

if (isset($_POST['encrypt_now'])) {
    $user       = $_SESSION['username'];
    $password   = $_POST["pwdfile"];
    $key128     = substr(hash('sha256', $password), 0, 16);
    $key256     = substr(hash('sha256', $password), 0, 32);
    $desc       = mysqli_real_escape_string($connect, $_POST['desc']);
    $alg_used   = $_POST['algorithm']; // expected value: AES-128 or AES-256

    $file_tmp   = $_FILES['file']['tmp_name'];
    $file_name  = rand(1000,100000)."-".$_FILES['file']['name'];
    $final_name = strtolower(str_replace(' ', '-', $file_name));
    $file_size  = filesize($file_tmp);
    $size_kb    = $file_size / 1024;
    $file_ext   = pathinfo($final_name, PATHINFO_EXTENSION);

    if (!in_array($file_ext, ['txt', 'docx', 'pptx', 'pdf'])) {
        echo "<script>alert('File format tidak didukung!'); window.location.href='encrypt.php';</script>";
        exit();
    }
    if ($size_kb > 3084) {
        echo "<script>alert('Ukuran file terlalu besar!'); window.location.href='encrypt.php';</script>";
        exit();
    }

    $plaintext   = file_get_contents($file_tmp);
    $start_time  = microtime(true);

    $cipher      = 'AES-128-ECB';
    $key         = $key128;
    if ($alg_used === 'AES-256') {
        $cipher  = 'AES-256-ECB';
        $key     = $key256;
    }

    $encrypted   = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA);
    $duration_ms = round((microtime(true) - $start_time) * 1000, 3);
    $hash        = hash('sha256', $plaintext);

    $save_path = 'hasil_ekripsi/' . pathinfo($final_name, PATHINFO_FILENAME) . '.rda';
    file_put_contents($save_path, $encrypted);

    $sql = "INSERT INTO file (username, file_name_source, file_name_finish, file_url, file_size, password, alg_used, process_time_ms, operation_type, hash_check, tgl_upload, status, keterangan)
            VALUES ('$user', '$final_name', '".basename($save_path)."', '$save_path', '$size_kb', '$key', '$alg_used', '$duration_ms', 'encrypt', '$hash', now(), '1', '$desc')";
    mysqli_query($connect, $sql) or die(mysqli_error($connect));

    echo "<script>alert('File berhasil dienkripsi!'); window.location.href='enkripsi.php';</script>";
}
?>