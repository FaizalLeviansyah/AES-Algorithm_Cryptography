<?php
// FILE: dashboard/user-process.php (NEW FILE)
// Backend script for adding and editing users.

session_start();
require_once __DIR__ . '/../config.php';

// Security check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    die("Access Denied.");
}

// --- ADD USER ---
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    // Use null if the division_id is empty, otherwise cast to int
    $division_id = !empty($_POST['division_id']) ? (int)$_POST['division_id'] : null;

    if (empty($username) || empty($fullname) || empty($password) || empty($role)) {
        $_SESSION['message'] = "Semua field (kecuali divisi) wajib diisi.";
        $_SESSION['message_type'] = "warning";
        header('Location: user-management.php');
        exit();
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connect->prepare("INSERT INTO users (username, fullname, password, role, division_id, job_title, join_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    // We can use the role as the job_title for simplicity
    $stmt->bind_param("ssssis", $username, $fullname, $hashed_password, $role, $division_id, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Pengguna baru berhasil ditambahkan.";
        $_SESSION['message_type'] = "success";
    } else {
        // Check for duplicate username error
        if ($connect->errno == 1062) {
             $_SESSION['message'] = "Gagal: Username '$username' sudah ada.";
        } else {
             $_SESSION['message'] = "Gagal menambahkan pengguna: " . $stmt->error;
        }
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    header('Location: user-management.php');
    exit();
}

// --- EDIT USER ---
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $division_id = !empty($_POST['division_id']) ? (int)$_POST['division_id'] : null;

    if (empty($username) || empty($fullname) || empty($role)) {
         $_SESSION['message'] = "Username, Nama Lengkap, dan Role wajib diisi.";
         $_SESSION['message_type'] = "warning";
         header('Location: user-management.php');
         exit();
    }

    // Check if password needs to be updated
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $connect->prepare("UPDATE users SET username=?, fullname=?, password=?, role=?, division_id=?, job_title=? WHERE id=?");
        $stmt->bind_param("ssssisi", $username, $fullname, $hashed_password, $role, $division_id, $role, $id);
    } else {
        // Update without changing password
        $stmt = $connect->prepare("UPDATE users SET username=?, fullname=?, role=?, division_id=?, job_title=? WHERE id=?");
        $stmt->bind_param("sssisi", $username, $fullname, $role, $division_id, $role, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data pengguna berhasil diupdate.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengupdate pengguna: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    header('Location: user-management.php');
    exit();
}


header('Location: user-management.php');
exit();
?>