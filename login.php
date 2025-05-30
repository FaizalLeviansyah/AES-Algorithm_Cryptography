<?php
session_start();
include('config.php');
$username = $_POST['username'];
$password = md5($_POST['password']);
$query = mysqli_query($connect, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$data = mysqli_fetch_array($query);
if ($data) {
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    header("Location: dashboard/");
} else {
    header("Location: index.php?error=1");
}
?>