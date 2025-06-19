<?php
// FILE: dashboard/division-process.php (NEW FILE)
// This is a backend script to handle the logic for adding, editing, and deleting divisions.
// It does not produce any HTML output.

session_start();
require_once __DIR__ . '/../config.php';

// Check if user is logged in and is a superadmin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    die("Access Denied.");
}

// --- ADD A NEW DIVISION ---
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    if (isset($_POST['division_name']) && !empty(trim($_POST['division_name']))) {
        $division_name = trim($_POST['division_name']);

        $stmt = $connect->prepare("INSERT INTO divisions (division_name) VALUES (?)");
        $stmt->bind_param("s", $division_name);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Divisi baru berhasil ditambahkan.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menambahkan divisi: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Nama divisi tidak boleh kosong.";
        $_SESSION['message_type'] = "warning";
    }
    header('Location: division-management.php');
    exit();
}

// --- EDIT AN EXISTING DIVISION ---
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (isset($_POST['id'], $_POST['division_name']) && !empty(trim($_POST['division_name']))) {
        $id = (int)$_POST['id'];
        $division_name = trim($_POST['division_name']);

        $stmt = $connect->prepare("UPDATE divisions SET division_name = ? WHERE id = ?");
        $stmt->bind_param("si", $division_name, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Data divisi berhasil diupdate.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal mengupdate divisi: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Data tidak lengkap untuk proses edit.";
        $_SESSION['message_type'] = "warning";
    }
    header('Location: division-management.php');
    exit();
}

// --- DELETE A DIVISION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        // Note: For better data integrity, you might first set users in this division to NULL
        // For now, we will just delete the division.
        $stmt = $connect->prepare("DELETE FROM divisions WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Divisi berhasil dihapus.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus divisi. Pastikan tidak ada pengguna yang terhubung dengan divisi ini. Error: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
    }
    header('Location: division-management.php');
    exit();
}

// If no action is matched, redirect away.
header('Location: division-management.php');
exit();
?>