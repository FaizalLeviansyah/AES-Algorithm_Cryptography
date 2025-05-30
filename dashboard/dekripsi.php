<?php
// File: dashboard/dekripsi.php (REVISI UI)

// 1. SERTAKAN AUTH_CHECK.PHP DI PALING ATAS
require_once __DIR__ . '/../auth_check.php'; // auth_check.php akan memulai session

// 2. SERTAKAN CONFIG.PHP JIKA BELUM DI-INCLUDE OLEH AUTH_CHECK.PHP
if (!isset($connect)) {
    require_once __DIR__ . '/../config.php';
}

// Data pengguna dari sesi (diasumsikan auth_check.php sudah mengisi ini)
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_job_title_session = ucfirst($user_role_session);

// Data user untuk sidebar (mengikuti pola dashboard/index.php)
// Jika $data_user sudah di-set oleh auth_check.php atau include lain, gunakan itu.
// Jika tidak, buat array $data_user dari variabel sesi.
if (!isset($data_user)) {
    $data_user = [
        'fullname' => $user_fullname_session,
        'job_title' => $user_job_title_session
        // 'profile_pic' bisa ditambahkan di sini jika sidebar memerlukannya secara eksplisit
        // dan tidak diambil langsung dari sidebar-nav-universal.php
    ];
}
// Path untuk gambar profil di sidebar, relatif dari dashboard/
$user_profile_pic_path_sidebar = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'img/contact/default-user.png';
$user_profile_pic_sidebar = file_exists(__DIR__ . '/../' . $user_profile_pic_path_sidebar) ? '../' . $user_profile_pic_path_sidebar : (file_exists(__DIR__ . '/' . $user_profile_pic_path_sidebar) ? $user_profile_pic_path_sidebar : 'img/contact/default-user.png');
// Jika path default tidak ada di img/contact/, coba ../img/contact/
if (!file_exists($user_profile_pic_sidebar) && strpos($user_profile_pic_sidebar, 'default-user.png') !== false) {
    $user_profile_pic_sidebar = '../img/contact/default-user.png';
     if (!file_exists($user_profile_pic_sidebar)) { // fallback final jika ../img/contact/default-user.png juga tidak ada
        $user_profile_pic_sidebar = 'img/contact/default-user.png'; // asumsikan ada di dashboard/img/contact/
     }
}


// Path CSS (relatif dari dashboard/dekripsi.php)
// Mengikuti pola path dari dashboard/index.php
$base_css_path = 'css/'; // Semua path CSS akan relatif terhadap direktori 'dashboard'
$custom_sidebar_css_path = $base_css_path . 'custom-style-sidebar.css';
$custom_sidebar_fixed_css_path = $base_css_path . 'custom-style-sidebar-fixed.css';
$include_custom_sidebar_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_css_path);
$include_custom_sidebar_fixed_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_fixed_css_path);

// Warna kartu aksi (jika diperlukan di halaman ini, jika tidak, bisa dihapus)
$action_card_decrypt_bg = "linear-gradient(135deg, #3498db, #2980b9)"; // Gradasi Biru

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar File & Dekripsi - Aplikasi Kriptografi AES</title>

    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"> <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $base_css_path; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>nalika-icon.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>meanmenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu-vertical.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>scrollbar/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>animate.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>normalize.css">
    <link rel="stylesheet" href="style.css"> <?php if ($include_custom_sidebar_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_css_path; ?>">
    <?php endif; ?>
    <?php if ($include_custom_sidebar_fixed_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_fixed_css_path; ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo $base_css_path; ?>responsive.css">
    <link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/css/jquery.dataTables.css">


    <script src="js/vendor/modernizr-2.8.3.min.js"></script>

    <style>
        /* --- MULAI CSS KUSTOM (DISALIN DARI DASHBOARD/INDEX.PHP REVISI V9) --- */
        :root {
            --header-height: 60px;
            --sidebar-width-normal: 250px;
            --sidebar-width-mini: 80px;

            --light-header-bg: #FFFFFF;
            --light-header-border: #E9EBF0;
            --light-content-bg: #F4F6F9;

            --light-text-primary: #343a40;
            --light-text-secondary: #6c757d;
            --light-icon-hover-bg: #f1f3f5;

            /* Sidebar TERANG dengan Header Gradasi */
            --sidebar-bg: #FFFFFF !important;
            --sidebar-header-gradient-start: #2ECC71 !important; /* Hijau (sesuai screenshot) */
            --sidebar-header-gradient-end:rgb(39, 50, 174) !important; /* Hijau Tua (sesuai screenshot) */
            --sidebar-header-text-color: #FFFFFF !important;
            --sidebar-text-color: #4B5158 !important; /* Teks menu gelap di sidebar terang */
            --sidebar-text-hover-color:rgb(39, 50, 174) !important; /* Warna aksen untuk hover */
            --sidebar-hover-bg: #E9F7EF !important;
            --sidebar-active-bg: #D4EFDF !important;
            --sidebar-accent-color: rgb(39, 50, 174)  !important;
            --sidebar-accent-color-rgb: 39, 50, 174; /* Untuk box-shadow */
            --sidebar-border-color: #E0E4E8 !important;

            --card-bg: #FFFFFF;
            --card-shadow: 0 2px 5px rgba(0,0,0,0.07);
            --card-hover-shadow: 0 4px 10px rgba(0,0,0,0.1);
            --card-border-radius: 8px;

            --text-color-default: #495057;
            --text-color-muted: #6c757d;

            /* Warna untuk Kartu Aksi Colorful (jika digunakan di halaman lain) */
            --action-card-encrypt-gradient: linear-gradient(135deg, #2ecc71, #27ae60);
            --action-card-decrypt-gradient: <?php echo $action_card_decrypt_bg; ?>;
            --action-card-analysis-gradient: linear-gradient(135deg, #9b59b6, #8e44ad);
            --action-card-text-color: #FFFFFF;
        }

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            background-color: var(--light-content-bg) !important;
            overflow-x: hidden;
        }

        /* 1. HEADER & SIDEBAR FIXED LAYOUT (DARI DASHBOARD/INDEX.PHP) */
        .left-sidebar-pro {
            background-color: var(--sidebar-bg) !important;
            position: fixed !important; top: 0 !important; left: 0 !important;
            height: 100vh !important; width: var(--sidebar-width-normal) !important;
            z-index: 1032 !important;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden; /* Sembunyikan scrollbar utama sidebar, akan ada di .left-custom-menu-adp-wrap */
            border-right: 1px solid var(--sidebar-border-color) !important;
            display: flex;
            flex-direction: column;
        }
        body.mini-navbar .left-sidebar-pro { width: var(--sidebar-width-mini) !important; }
        body.mini-navbar .sidebar-header .main-logo { display: none !important; }
        body.mini-navbar .sidebar-header strong { display: block !important; }
        body.mini-navbar .nalika-profile { display: none !important; } /* Profil disembunyikan di mini-navbar */
        body.mini-navbar .metismenu li a span:not(.mini-click-non),
        body.mini-navbar .metismenu li a .pull-right-container { display: none !important; }
        body.mini-navbar .metismenu li a { text-align: center !important; padding: 12px 0 !important;}
        body.mini-navbar .metismenu li a .fa,
        body.mini-navbar .metismenu li a .nalika-icon { margin-right: 0 !important; font-size: 1.3em !important; }

        .header-top-area {
            background: var(--light-header-bg) !important;
            height: var(--header-height) !important;
            min-height: var(--header-height) !important;
            width: calc(100% - var(--sidebar-width-normal)) !important;
            display: flex !important; align-items: center !important; padding: 0 !important;
            box-sizing: border-box !important; position: fixed !important;
            top: 0 !important; left: var(--sidebar-width-normal) !important;
            z-index: 1030 !important;
            border-bottom: 1px solid var(--light-header-border) !important;
            box-shadow: var(--card-shadow) !important;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .all-content-wrapper {
            padding-top: var(--header-height) !important;
            margin-left: var(--sidebar-width-normal) !important;
            background: var(--light-content-bg) !important;
            min-height: calc(100vh - 56px); /* 56px adalah perkiraan tinggi footer, sesuaikan */
            box-sizing: border-box; position: relative;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
            padding-bottom: 70px; /* Ruang untuk footer */
        }
        
        body.mini-navbar .header-top-area {
            left: var(--sidebar-width-mini) !important;
            width: calc(100% - var(--sidebar-width-mini)) !important;
        }
        body.mini-navbar .all-content-wrapper {
            margin-left: var(--sidebar-width-mini) !important;
        }

        .header-top-wraper {
            width: 100% !important; height: 100% !important;
            padding: 0 20px !important;
            display: flex !important; align-items: center !important;
            justify-content: space-between !important; box-sizing: border-box !important;
        }

        .header-left-info { display: flex; align-items: center; flex-shrink: 0; }
        .menu-switcher-pro .navbar-btn {
            color: var(--light-text-secondary) !important; background-color: transparent !important; border: none !important;
            font-size: 1.5em !important; padding: 0 !important; margin-right: 15px !important; line-height: var(--header-height) !important;
        }
        .menu-switcher-pro .navbar-btn:hover, .menu-switcher-pro .navbar-btn:focus { color: var(--sidebar-accent-color) !important; }
        .dashboard-title-header {
            color: var(--light-text-primary) !important; margin: 0 !important; font-size: 1.25em !important;
            font-weight: 500 !important; line-height: var(--header-height) !important; white-space: nowrap;
        }

        .header-right-info { display: flex; align-items: center; justify-content: flex-end; flex-grow: 1; overflow: visible; }
        .header-right-info .navbar-nav { display: flex; align-items: center; padding-left: 0; margin-bottom: 0; }
        .header-right-info .navbar-nav > li { margin-left: 5px; list-style: none; }
        .header-right-info .navbar-nav > li:first-child { margin-left: 0; }
        .header-right-info .nav > li > a { /* Untuk ikon search, notif, dll */
            color: var(--light-text-secondary) !important; padding: 7px !important; display: flex; align-items: center;
            border-radius: 50% !important; height: 34px !important; width: 34px !important;
            justify-content: center; transition: background-color 0.2s ease, color 0.2s ease;
        }
        .header-right-info .nav > li > a:hover, .header-right-info .nav > li > a:focus {
            color: var(--light-text-primary) !important;
            background-color: var(--light-icon-hover-bg) !important;
        }
        .header-right-info .nav > li > a > i { font-size: 1.1em !important; }

        /* STYLING UNTUK USER PROFILE AREA DI HEADER (MENGIKUTI dashboard/index.php) */
        .header-right-info .user-profile-area > a {
            padding: 5px 10px !important; height: auto !important; border-radius: 20px !important;
            width: auto !important;
            background-color: transparent !important; /* Tombol logout agar transparan */
        }
        .header-right-info .user-profile-area > a:hover,
        .header-right-info .user-profile-area > a:focus {
            background-color: var(--light-icon-hover-bg) !important;
        }
        .header-right-info .user-profile-area button { /* Styling untuk tombol logout */
            background-color: var(--sidebar-accent-color) !important;
            color: white !important;
            border: none !important;
            padding: 8px 15px !important;
            border-radius: 20px !important;
            font-size: 0.85em !important;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }
        .header-right-info .user-profile-area button:hover {
            opacity: 0.85;
        }
        /* Jika menggunakan gambar profil dan nama di header (seperti di dekripsi.php sebelumnya) */
        .header-right-info .user-profile-area img.profile-img-header { width: 28px; height: 28px; border-radius: 50%; margin-right: 8px; object-fit: cover; }
        .user-profile-details { display: flex; flex-direction: column; justify-content: center; text-align: left; line-height: 1.2; }
        .header-right-info .user-profile-area .admin-name { font-weight: 500 !important; font-size: 0.85em !important; color: var(--light-text-primary) !important; white-space: nowrap; }
        .header-right-info .user-profile-area .admin-title-header { font-size: 0.75em !important; color: var(--light-text-secondary) !important; white-space: nowrap; }
        .header-right-info .user-profile-area .fa-angle-down { margin-left: 6px !important; font-size: 0.8em !important; color: var(--light-text-secondary) !important; }

        .author-log.dropdown-menu {
            right: 0px !important; left: auto !important;
            top: calc(100% + 10px) !important; /* Sedikit jarak dari header */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
            border: 1px solid var(--light-header-border) !important;
            border-radius: var(--card-border-radius) !important; margin-top: 0 !important;
            padding: 8px 0 !important; background-color: #fff !important;
        }
        .author-log.dropdown-menu > li > a { padding: 8px 18px !important; font-size: 0.9em !important; color: var(--light-text-primary) !important; display:flex; align-items:center; }
        .author-log.dropdown-menu > li > a:hover { background-color: var(--light-icon-hover-bg) !important; color: var(--sidebar-accent-color) !important; }
        .author-log.dropdown-menu > li > a .fa { color: var(--light-text-secondary) !important; margin-right: 10px; width:16px; text-align:center; }
        .author-log.dropdown-menu > li > a:hover .fa { color: var(--sidebar-accent-color) !important; }
        .author-log.dropdown-menu .divider { margin: 6px 0; background-color: var(--light-header-border); }


        /* SIDEBAR STYLING (TERANG DENGAN HEADER GRADASI - DARI DASHBOARD/INDEX.PHP) */
        .sidebar-header {
            padding: 0 !important;
            height: auto !important;
            min-height: calc(var(--header-height) + 70px) !important;
            background: var(--sidebar-header-gradient-start); /* Fallback */
            background: linear-gradient(135deg, var(--sidebar-header-gradient-end), var(--sidebar-header-gradient-start)) !important;
            text-align: center !important; display: flex !important; flex-direction: column !important;
            align-items: center !important; justify-content: center !important; box-sizing: border-box !important;
            border-bottom: none !important;
            color: var(--sidebar-header-text-color) !important;
        }
        .sidebar-header > a { /* Link logo */
            display: block !important; line-height: normal !important; margin-top: 15px !important;
            padding: 0 !important; border: none !important; outline: none !important;
            box-shadow: none !important; text-decoration: none !important;
        }
        .sidebar-header .main-logo {
            max-width: 150px !important;
            max-height: 46px !important;
            height: auto !important; display: block !important; object-fit: contain !important;
            margin: 0 auto 10px auto !important;
            filter: brightness(0) invert(1); /* Membuat logo putih */
        }
        .sidebar-header strong { display: none; }
        .sidebar-header strong img { max-height: 30px; filter: brightness(0) invert(1); }

        .nalika-profile { /* Profil di Sidebar */
            padding: 0 15px 15px 15px !important;
            text-align: center !important;
            border-bottom: 1px solid var(--sidebar-border-color) !important;
            background: var(--sidebar-bg) !important;
        }
        .nalika-profile .profile-dtl {
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2); /* Garis pemisah halus di atas foto profil jika header gradasi */
        }
        .nalika-profile .profile-dtl img.profile-img-sidebar { /* Nama class spesifik untuk gambar profil sidebar */
            width: 60px !important; height: 60px !important; border-radius: 50% !important;
            margin-bottom: 10px !important; border: 2px solid var(--sidebar-accent-color) !important;
            object-fit: cover;
        }
        .nalika-profile .profile-dtl h2 { /* Nama User di Sidebar */
            color: var(--light-text-primary) !important; font-size: 0.95em !important;
            margin-bottom: 3px !important; font-weight: 500 !important;
        }
        .nalika-profile .profile-dtl .designation { /* Jabatan di Sidebar */
            font-size: 0.8em !important; color: var(--light-text-secondary) !important; display: block;
        }

        /* Menu Sidebar (Latar Terang - DARI DASHBOARD/INDEX.PHP) */
        .left-custom-menu-adp-wrap {
            flex-grow: 1;
            overflow-y: auto;
            background-color: var(--sidebar-bg) !important;
        }
        .metismenu { background-color: var(--sidebar-bg) !important; padding-top:10px; padding-bottom: 20px; }
        .metismenu li { background-color: var(--sidebar-bg) !important; }
        .metismenu li a {
            color: var(--sidebar-text-color) !important;
            padding: 12px 20px !important;
            font-size: 0.9em !important;
            border-bottom: none !important; /* Hilangkan border bawah antar menu item */
            display: flex; align-items: center;
            transition: background-color 0.2s ease, color 0.2s ease, border-left-color 0.2s ease;
        }
        .metismenu li:last-child a { border-bottom: none !important; }
        .metismenu li a:hover,
        .metismenu li.active > a {
            background-color: var(--sidebar-hover-bg) !important;
            color: var(--sidebar-text-hover-color) !important;
            border-left: 4px solid var(--sidebar-accent-color) !important;
            padding-left: 16px !important; /* (20px - 4px) */
        }
        .metismenu li.active > a { background-color: var(--sidebar-active-bg) !important; font-weight: 500; }
        .metismenu li a .fa, .metismenu li a .nalika-icon {
            margin-right: 12px !important; font-size: 1.05em !important;
            width: 20px; text-align: center; flex-shrink: 0;
            color: var(--sidebar-text-color) !important; /* Warna ikon menu */
            transition: color 0.2s ease;
        }
        .metismenu li a:hover .fa, .metismenu li a:hover .nalika-icon,
        .metismenu li.active > a .fa, .metismenu li.active > a .nalika-icon {
            color: var(--sidebar-text-hover-color) !important;
        }
        .metismenu ul { /* Submenu */
            border-left: 4px solid var(--sidebar-border-color, #E0E4E8);
            margin-left: 18px; /* Sesuaikan indentasi submenu */
            padding-left: 0;
        }
        .metismenu ul a { padding-left: 20px !important; font-size: 0.85em !important; background-color: #fdfdfd !important; border-bottom-style: dashed !important; border-bottom-color: #f0f0f0 !important; }
        .metismenu ul a:hover, .metismenu ul li.active > a { background-color: var(--sidebar-hover-bg) !important; padding-left: 16px !important; }

        /* KONTEN AREA (DARI DASHBOARD/INDEX.PHP) */
        .content-wrap { /* Wrapper umum untuk konten di dalam all-content-wrapper */
            padding: 20px 15px; /* Padding default untuk halaman konten */
        }
        /* Breadcrumbs Styling (DARI DASHBOARD/INDEX.PHP jika ada, atau styling baru) */
        .breadcome-area-custom {
            background-color: transparent; /* Atau var(--light-content-bg) */
            padding: 15px 0px; /* Atur padding atas bawah */
            margin-bottom: 0px; /* Hilangkan margin bawah jika .content-wrap sudah memberi padding */
            border-bottom: 1px solid var(--light-header-border);
            margin-left: -15px; /* Menyesuaikan padding dari .content-wrap */
            margin-right: -15px;
            padding-left: 15px;
            padding-right: 15px;
            margin-top: -20px; /* Menyesuaikan padding dari .content-wrap */
            margin-bottom: 20px;
        }
        .breadcome-list-custom {
            padding: 0; margin: 0; list-style: none;
            display: flex; align-items: center; font-size: 0.9em;
        }
        .breadcome-list-custom li a {
            color: var(--sidebar-accent-color); text-decoration: none;
            transition: color 0.2s ease;
        }
        .breadcome-list-custom li a:hover { color: var(--light-text-primary); }
        .breadcome-list-custom li .bread-slash { margin: 0 10px; color: var(--light-text-secondary); }
        .breadcome-list-custom li.active { color: var(--light-text-primary); font-weight: 500; }

        /* Styling untuk tabel dan konten halaman dekripsi (Contoh dari dekripsi.php sebelumnya, disesuaikan) */
        .table-container-card {
            background-color: var(--card-bg);
            padding: 25px 30px;
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            margin-top: 0; /* Dihapus karena .content-wrap sudah memberi jarak */
        }
        .table-container-card h2.table-title {
            color: var(--light-text-primary);
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.5em; /* Sedikit lebih kecil dari dashboard utama */
            font-weight: 500;
        }
        .table-container-card p.table-subtitle {
            color: var(--text-color-muted);
            font-size:0.9em;
            margin-bottom:25px;
            border-bottom: 1px solid var(--light-header-border);
            padding-bottom: 15px;
        }
        .table thead th {
            background-color: #f8f9fa; /* Latar header tabel */
            color: var(--light-text-primary);
            font-weight: 500;
            border-bottom-width: 2px;
            border-color: var(--light-header-border);
            font-size:0.85em;
            text-transform: uppercase;
            padding: 10px 12px;
        }
        .table tbody tr:hover { background-color: #f1f3f5; } /* Hover baris tabel */
        .table td, .table th {
            vertical-align: middle;
            font-size: 0.9em;
            padding: 10px 12px;
        }
        .table td .badge { font-size: 0.8em; padding: 0.4em 0.6em;}
        .btn-action-decrypt, .btn-action-encrypt-alt { /* Tombol Aksi di Tabel */
            color: white !important;
            padding: 6px 12px;
            font-size: 0.85em;
            border-radius: 5px;
            transition: opacity 0.2s ease;
        }
        .btn-action-decrypt {
            background-color: var(--sidebar-accent-color) !important;
            border-color: var(--sidebar-accent-color) !important;
        }
        .btn-action-encrypt-alt {
            background-color: #28a745 !important; /* Hijau untuk enkripsi */
            border-color: #28a745 !important;
        }
        .btn-action-decrypt:hover, .btn-action-encrypt-alt:hover { opacity: 0.85; }
        .btn-action-decrypt .fa, .btn-action-encrypt-alt .fa { margin-right: 5px; }

        .status-encrypted { color: #dc3545; font-weight: 500; } /* Merah untuk terenkripsi */
        .status-decrypted { color: #28a745; font-weight: 500; } /* Hijau untuk terdekripsi */
        .status-encrypted .fa, .status-decrypted .fa { margin-right: 4px;}

        .alert-page-message { /* Untuk pesan di atas tabel */
            margin-bottom: 20px;
            border-radius: var(--card-border-radius);
            padding: 12px 18px;
            font-size: 0.9em;
        }
        .alert-page-message .fa { margin-right: 8px; }

        /* DataTables styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.88em;
            color: var(--text-color-muted);
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px 8px;
            background-color: #fff;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 8px;
            margin-left: 5px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.4em 0.8em;
            margin-left: 2px;
            border-radius: 4px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--sidebar-accent-color) !important;
            color: white !important;
            border-color: var(--sidebar-accent-color) !important;
        }
         .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            border-color: #ddd !important;
            color: var(--light-text-primary) !important;
        }


        .footer-copyright-area {
            background: var(--card-bg, #fff) !important;
            padding: 18px 0 !important;
            border-top: 1px solid var(--light-header-border) !important;
            position: fixed; /* Footer dibuat fixed */
            bottom: 0;
            width: calc(100% - var(--sidebar-width-normal)); /* Disesuaikan dengan JS */
            left: var(--sidebar-width-normal); /* Disesuaikan dengan JS */
            z-index: 1000;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.mini-navbar .footer-copyright-area {
            left: var(--sidebar-width-mini) !important;
            width: calc(100% - var(--sidebar-width-mini)) !important;
        }
        .footer-copy-right p {
            color: var(--text-color-muted) !important;
            font-size: 0.85em; margin-bottom:0; text-align: center;
        }

        /* RESPONSIVITAS (DARI DASHBOARD/INDEX.PHP) */
        @media (max-width: 991px) {
            .header-right-info .nav > li.d-none.d-md-flex { display: none !important; } /* Sembunyikan search icon di mobile */
            .header-right-info .user-profile-details { display: none !important; } /* Sembunyikan nama & title di header mobile */
            .header-right-info .user-profile-area img.profile-img-header { margin-right:0; }
            .dashboard-title-header { font-size: 1.1em !important; }
        }
        @media (max-width: 767px) {
            .dashboard-title-header { font-size: 1em !important; white-space: normal; max-width: 120px; overflow: hidden; text-overflow: ellipsis; }
            .header-top-wraper { padding: 0 10px !important; }
            .header-right-info .navbar-nav > li { margin-left: 2px !important; }
            .header-right-info .nav > li > a { padding: 8px 5px !important; height: 34px !important; width: 34px !important; }
            .header-right-info .nav > li > a > i { font-size: 1.05em !important; }
            .menu-switcher-pro .navbar-btn { margin-right: 8px !important;}
            .footer-copyright-area {
                width: 100% !important;
                left: 0 !important;
            }
            .all-content-wrapper {
                 margin-left: 0 !important; /* Full width content on mobile when sidebar is hidden */
            }
             body:not(.mini-navbar) .left-sidebar-pro { /* Sidebar overlay on mobile */
                left: -250px !important; /* Initially hidden */
                z-index: 1035;
             }
             body.mini-navbar .left-sidebar-pro { /* When toggled */
                left: 0 !important;
                width: var(--sidebar-width-normal) !important; /* Full width sidebar */
             }
             body.mini-navbar .header-top-area,
             body.mini-navbar .all-content-wrapper,
             body.mini-navbar .footer-copyright-area {
                margin-left: 0 !important; /* Ensure content is not pushed */
                left: 0 !important;
                width: 100% !important;
             }
        }
        @media (max-width: 480px) {
            .dashboard-title-header { font-size: 0.95em !important; max-width:100px; }
            /* User profile area di header untuk dashboard/index.php (tombol logout) */
            .header-right-info .user-profile-area button {
                padding: 6px 10px !important;
                font-size: 0.8em !important;
            }
        }
        /* --- AKHIR CSS KUSTOM --- */
    </style>
</head>
<body class=""> <div class="left-sidebar-pro">
        <nav id="sidebar" class="">
            <div class="sidebar-header">
                <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo PALW" /></a>
                <strong><img src="img/logo/logosn.png" alt="Logo Small PALW" /></strong>
            </div>
            <div class="nalika-profile">
                <div class="profile-dtl">
                    <a href="#"><img class="profile-img-sidebar" src="<?php echo htmlspecialchars($user_profile_pic_sidebar); ?>" alt="Foto Profil Pengguna" /></a>
                    <h2><?php echo htmlspecialchars($data_user['fullname']); ?> <span class="designation icon"><?php echo htmlspecialchars($data_user['job_title']); ?></span></h2>
                </div>
            </div>
            <div class="left-custom-menu-adp-wrap comment-scrollbar">
                <nav class="sidebar-nav left-sidebar-menu-pro">
                    <?php include('sidebar-nav-universal.php'); // Pastikan path ini benar ?>
                </nav>
            </div>
        </nav>
    </div>

    <div class="all-content-wrapper">
        <div class="header-top-area">
            <div class="header-top-wraper">
                <div class="header-left-info">
                    <div class="menu-switcher-pro">
                        <button type="button" id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
                            <i class="nalika-menu-task"></i>
                        </button>
                    </div>
                    <h1 class="dashboard-title-header">Daftar File & Dekripsi</h1>
                </div>
                <div class="header-right-info">
                    <ul class="nav navbar-nav mai-top-nav header-right-menu">
                        <li class="nav-item d-none d-md-flex"> {/* Search icon, sesuaikan dengan dashboard/index.php */}
                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><i class="nalika-search" aria-hidden="true"></i></a>
                            <div role="menu" class="dropdown-menu search- мл animated zoomIn">
                                <div class="search-active-menu"><form action="#"><input type="text" placeholder="Cari disini..." class="form-control"><a href="#"><i class="fa fa-search"></i></a></form></div>
                            </div>
                        </li>
                        {/* Area User Profile disamakan dengan dashboard/index.php */}
                        <li class="nav-item user-profile-area">
                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                <button>Logout</button> {/* Sesuai dengan dashboard/index.php */}
                            </a>
                            <ul role="menu" class="dropdown-header-top author-log dropdown-menu animated zoomIn">
                                {/* Jika ingin ada link profil/pengaturan, bisa ditambahkan di sini atau di sidebar-nav-universal.php */}
                                <li><a href="../logout.php"><span class="fa fa-sign-out author-log-ic"></span> Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-wrap">
            <div class="breadcome-area-custom">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="breadcome-list-custom">
                                <li><a href="index.php">Dashboard</a> <span class="bread-slash">/</span></li>
                                <li class="active">Daftar File & Dekripsi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-container-card">
                            <h2 class="table-title">Manajemen File Enkripsi/Dekripsi</h2>
                            <p class="table-subtitle">
                                Berikut adalah daftar file yang telah Anda proses. Anda dapat mendekripsi file yang terenkripsi.
                            </p>

                            <?php
                            if (isset($_SESSION['dekripsi_message'])) {
                                $message_type_list = isset($_SESSION['dekripsi_message_type']) && $_SESSION['dekripsi_message_type'] == 'error' ? 'danger' : ($_SESSION['dekripsi_message_type'] == 'info' ? 'info' : 'success');
                                $alert_icon_list = $message_type_list == 'danger' ? 'fa-times-circle' : ($message_type_list == 'info' ? 'fa-info-circle' : 'fa-check-circle');
                                echo '<div class="alert alert-' . $message_type_list . ' alert-dismissible fade show alert-page-message" role="alert">';
                                echo '<i class="fa ' . $alert_icon_list . '" aria-hidden="true"></i> ' . htmlspecialchars($_SESSION['dekripsi_message']);
                                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                echo '</div>';
                                unset($_SESSION['dekripsi_message']);
                                unset($_SESSION['dekripsi_message_type']);
                            }
                            ?>

                            <div class="table-responsive">
                                <table id="fileTable" class="table table-striped table-hover" style="width:100%"> <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File Asli</th>
                                            <th>Nama File Proses</th>
                                            <th>Ukuran (KB)</th>
                                            <th>Tgl. Upload</th>
                                            <th>Algoritma</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $username_filter = $_SESSION['username']; // username dari auth_check.php
                                        // Pastikan $role sudah di-set dari auth_check.php
                                        $current_user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

                                        $nama_kolom_ukuran_file = 'file_size'; // Ganti jika nama kolom berbeda

                                        if ($current_user_role == 'superadmin' || $current_user_role == 'admin') {
                                            $query_files_sql = "SELECT id_file, file_name_source, file_name_finish, {$nama_kolom_ukuran_file}, tgl_upload, status, alg_used FROM file ORDER BY tgl_upload DESC";
                                            $stmt_files = mysqli_prepare($connect, $query_files_sql);
                                        } else {
                                            $query_files_sql = "SELECT id_file, file_name_source, file_name_finish, {$nama_kolom_ukuran_file}, tgl_upload, status, alg_used FROM file WHERE username = ? ORDER BY tgl_upload DESC";
                                            $stmt_files = mysqli_prepare($connect, $query_files_sql);
                                            mysqli_stmt_bind_param($stmt_files, "s", $username_filter);
                                        }

                                        if ($stmt_files) {
                                            mysqli_stmt_execute($stmt_files);
                                            $result_files = mysqli_stmt_get_result($stmt_files);

                                            if (mysqli_num_rows($result_files) > 0) {
                                                while ($file_data = mysqli_fetch_array($result_files, MYSQLI_ASSOC)) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo htmlspecialchars($file_data['file_name_source']); ?></td>
                                                        <td><?php echo htmlspecialchars($file_data['file_name_finish']); ?></td>
                                                        <td><?php echo htmlspecialchars(number_format($file_data[$nama_kolom_ukuran_file], 2)); ?></td>
                                                        <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($file_data['tgl_upload']))); ?></td>
                                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($file_data['alg_used']); ?></span></td>
                                                        <td>
                                                            <?php
                                                            if ($file_data['status'] == 1) {
                                                                echo "<span class='status-encrypted'><i class='fa fa-lock'></i> Terenkripsi</span>";
                                                            } elseif ($file_data['status'] == 2) {
                                                                echo "<span class='status-decrypted'><i class='fa fa-unlock-alt'></i> Terdekripsi</span>";
                                                            } else {
                                                                echo "<span><i class='fa fa-question-circle'></i> Tidak Diketahui</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($file_data['status'] == 1): ?>
                                                                <a href="decrypt-file.php?id_file=<?php echo $file_data['id_file']; ?>" class="btn btn-sm btn-action-decrypt" title="Dekripsi File Ini">
                                                                    <i class="fa fa-key"></i> Dekripsi
                                                                </a>
                                                            <?php elseif ($file_data['status'] == 2): ?>
                                                                <a href="enkripsi.php" class="btn btn-sm btn-action-encrypt-alt" title="Enkripsi File Baru">
                                                                    <i class="fa fa-shield"></i> Enkripsi Ulang
                                                                </a>
                                                            <?php else: ?>
                                                                <span>-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>Belum ada data file.</td></tr>";
                                            }
                                            mysqli_stmt_close($stmt_files);
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>Gagal mengambil data file: " . mysqli_error($connect) . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <div class="footer-copyright-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer-copy-right">
                            <p>Copyright © <?php echo date("Y"); ?> Aplikasi Kriptografi AES by <?php echo htmlspecialchars($data_user['fullname']); ?>. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.meanmenu.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/scrollbar/mCustomScrollbar-active.js"></script>
    <script src="js/metisMenu/metisMenu.min.js"></script>
    <script src="js/metisMenu/metisMenu-active.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#fileTable').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                "pageLength": 10,
                "responsive": true,
                "columnDefs": [ { "orderable": false, "targets": 7 } ] // Kolom aksi tidak bisa diorder
            });

            // SALIN FUNGSI adjustMainLayout DARI DASHBOARD/INDEX.PHP
            function adjustMainLayout() {
                var sidebarPro = $('.left-sidebar-pro');
                var sidebarWidth = 0;
                var rootStyles = getComputedStyle(document.documentElement);
                var defaultSidebarNormalWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-normal').trim()) || 250;
                var defaultSidebarMiniWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-mini').trim()) || 80;
                var headerHeight = parseFloat(rootStyles.getPropertyValue('--header-height').trim()) || 60;
                var footerArea = $('.footer-copyright-area');
                var footerHeight = (footerArea.length > 0 && footerArea.css('position') === 'fixed') ? (footerArea.outerHeight() || 56) : 0;


                if ($(window).width() >= 768) { // Hanya berlaku untuk layar desktop
                    if (sidebarPro.length > 0 && sidebarPro.is(':visible')) {
                        if ($('body').hasClass('mini-navbar')) {
                            sidebarWidth = defaultSidebarMiniWidth;
                        } else {
                            sidebarWidth = defaultSidebarNormalWidth;
                        }
                    }
                } else { // Untuk mobile, sidebarWidth dianggap 0 karena overlay atau tersembunyi
                    sidebarWidth = 0;
                     // Jika body.mini-navbar aktif di mobile (artinya sidebar terbuka overlay)
                    if ($('body').hasClass('mini-navbar')) {
                        // Tidak perlu mengubah sidebarWidth karena layout content tidak bergeser
                    }
                }


                var headerTopArea = $('.header-top-area');
                var allContentWrapper = $('.all-content-wrapper');

                if (headerTopArea.css('position') === 'fixed') {
                     if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) {
                        headerTopArea.css({
                            'left': sidebarWidth + 'px',
                            'width': 'calc(100% - ' + sidebarWidth + 'px)'
                        });
                    } else { // mobile dengan sidebar overlay terbuka
                         headerTopArea.css({ 'left': '0px', 'width': '100%'});
                    }
                }
                if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) {
                    allContentWrapper.css({
                        'margin-left': sidebarWidth + 'px',
                        'padding-top': headerHeight + 'px',
                        'padding-bottom': (footerHeight + 20) + 'px' // 20px extra space
                    });
                } else { // mobile dengan sidebar overlay terbuka
                     allContentWrapper.css({
                        'margin-left': '0px',
                        'padding-top': headerHeight + 'px',
                        'padding-bottom': (footerHeight + 20) + 'px'
                    });
                }


                if (footerArea.length > 0 && footerArea.css('position') === 'fixed') {
                    if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) {
                        footerArea.css({
                            'left': sidebarWidth + 'px',
                            'width': 'calc(100% - ' + sidebarWidth + 'px)'
                        });
                    } else { // mobile dengan sidebar overlay terbuka
                        footerArea.css({ 'left': '0px', 'width': '100%'});
                    }
                }
            }

            adjustMainLayout();

            var bodyNode = document.querySelector('body');
            if (bodyNode) {
                var observer = new MutationObserver(function(mutationsList, observer) {
                    for(let mutation of mutationsList) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            setTimeout(adjustMainLayout, 50);
                            // Perbarui juga status tombol sidebar jika layar mobile
                            if ($(window).width() < 768) {
                                if ($('body').hasClass('mini-navbar')) { // Sidebar terbuka
                                    $('#sidebarCollapse').addClass('active');
                                } else { // Sidebar tertutup
                                    $('#sidebarCollapse').removeClass('active');
                                }
                            }
                            break;
                        }
                    }
                });
                observer.observe(bodyNode, { attributes: true });
            }
            $(window).on('resize', function() {
                 setTimeout(adjustMainLayout, 50);
            });

            // Toggle sidebar untuk mobile (jika #sidebarCollapse diklik)
            // main.js mungkin sudah menangani ini, tapi ini untuk memastikan
            $('#sidebarCollapse').on('click', function () {
                if ($(window).width() < 768) { // Hanya untuk mobile
                    $('body').toggleClass('mini-navbar'); // Ini akan memicu MutationObserver
                    // Tidak perlu adjustMainLayout() langsung di sini karena observer akan melakukannya
                }
                // Untuk desktop, fungsionalitas toggle class .mini-navbar biasanya sudah ada di main.js
            });

            
        });
    </script>
</body>
</html>