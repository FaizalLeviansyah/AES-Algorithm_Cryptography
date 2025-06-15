<?php
// File: login-process.php (REVISED AND SECURE)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php'; 

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    // Use prepared statements to prevent SQL Injection
    $query_sql = "SELECT id, username, password, fullname, role, job_title FROM users WHERE username = ?";
    $stmt = mysqli_prepare($connect, $query_sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        // Securely verify the password
        if ($data && password_verify($password_input, $data['password'])) {
            // Login successful
            session_regenerate_id(true); // Prevent session fixation attacks

            $_SESSION['user_id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['fullname'] = $data['fullname'];
            $_SESSION['job_title'] = $data['job_title'];

            header('Location: dashboard/');
            exit; 
        } else {
            // Login failed
            $_SESSION['login_error'] = "Username atau Password salah!";
            header('Location: index.php'); 
            exit;
        }
    } else {
        // Database error
        error_log("MySQLi prepare failed: " . mysqli_error($connect));
        $_SESSION['login_error'] = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>