<?php
// File: auth_check.php

// ini_set('display_errors', 1); // REMOVE OR COMMENT OUT THIS LINE AFTER FIXING
// error_reporting(E_ALL);     // REMOVE OR COMMENT OUT THIS LINE AFTER FIXING

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi database tersedia
if (!isset($connect)) {
    require_once __DIR__ . '/config.php';
}

// Cek login
if (empty($_SESSION['username'])) {
    header('Location: /index.php');
    exit;
}

// Ensure role and fullname are set in session, and refresh if needed
if (!isset($_SESSION['role']) || !isset($_SESSION['fullname']) || (isset($_SESSION['username']) && !isset($_SESSION['last_role_check_time']))) {
    $username = $_SESSION['username'];
    // Use prepared statement for security
    // Removed 'profile_pic' from the SELECT query
    $query_user_info_sql = "SELECT fullname, job_title, role FROM users WHERE username = ?";
    $stmt_user_info = mysqli_prepare($connect, $query_user_info_sql);
    if ($stmt_user_info) {
        mysqli_stmt_bind_param($stmt_user_info, "s", $username);
        mysqli_stmt_execute($stmt_user_info);
        $result_user_info = mysqli_stmt_get_result($stmt_user_info);
        $user_info = mysqli_fetch_array($result_user_info, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_user_info);

        if ($user_info) {
            $_SESSION['fullname'] = $user_info['fullname'];
            $_SESSION['role'] = $user_info['role'];
            $_SESSION['job_title'] = $user_info['job_title'];
            // $_SESSION['profile_pic'] is no longer set here as the column is removed
            $_SESSION['last_role_check_time'] = time();
        } else {
            // User not found, clear session and redirect to login
            session_unset();
            session_destroy();
            header('Location: /index.php');
            exit;
        }
    } else {
        error_log("Database error in auth_check.php: " . mysqli_error($connect));
        die("System error. Please try again later.");
    }
}