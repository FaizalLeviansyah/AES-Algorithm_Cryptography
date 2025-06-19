<?php
// File: dashboard-analisis-aes.php (REVISI UI - Menampilkan Data "Sepenuhnya" dengan Template & DataTables)

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Otentikasi dan konfigurasi standar
require_once __DIR__ . '/../auth_check.php';
if (!isset($connect)) {
    require_once __DIR__ . '/../config.php';
}

// Update last activity (gunakan prepared statement)
if (isset($_SESSION['username'])) {
    $stmt_update_activity = mysqli_prepare($connect, "UPDATE users SET last_activity=now() WHERE username=?");
    if ($stmt_update_activity) {
        mysqli_stmt_bind_param($stmt_update_activity, "s", $_SESSION['username']);
        mysqli_stmt_execute($stmt_update_activity);
        mysqli_stmt_close($stmt_update_activity);
    }
}

// 2. Data pengguna dari sesi
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_job_title_session = ucfirst($user_role_session);

$data_user = [
    'fullname' => $user_fullname_session,
    'job_title' => $user_job_title_session,
];

// Path gambar profil sidebar
$user_profile_pic_path_session = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'img/contact/default-user.png';
$user_profile_pic_sidebar = file_exists(__DIR__ . '/../' . $user_profile_pic_path_session) ? '../' . $user_profile_pic_path_session : (file_exists(__DIR__ . '/' . $user_profile_pic_path_session) ? $user_profile_pic_path_session : 'img/contact/default-user.png');
if (!file_exists($user_profile_pic_sidebar) && strpos($user_profile_pic_sidebar, 'default-user.png') !== false) {
    $user_profile_pic_sidebar = 'img/contact/default-user.png';
    if (!file_exists(__DIR__ . '/' . $user_profile_pic_sidebar)) {
         $user_profile_pic_sidebar = '../img/contact/default-user.png';
         if (!file_exists(__DIR__ . '/../' . $user_profile_pic_sidebar) && !file_exists(__DIR__ . '/' . $user_profile_pic_sidebar)) {
             $user_profile_pic_sidebar = 'img/contact/default-user.png';
         }
    }
}

// Path CSS dasar
$base_css_path_analisis = 'css/';
$custom_sidebar_css_path_analisis = $base_css_path_analisis . 'custom-style-sidebar.css';
$custom_sidebar_fixed_css_path_analisis = $base_css_path_analisis . 'custom-style-sidebar-fixed.css';
$include_custom_sidebar_css_analisis = file_exists(__DIR__ . '/' . $custom_sidebar_css_path_analisis);
$include_custom_sidebar_fixed_css_analisis = file_exists(__DIR__ . '/' . $custom_sidebar_fixed_css_path_analisis);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kinerja AES - Aplikasi Kriptografi AES</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>nalika-icon.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>meanmenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>metisMenu/metisMenu-vertical.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>scrollbar/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>animate.css">
    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>normalize.css">
    <link rel="stylesheet" href="style.css"> 

    <?php if ($include_custom_sidebar_css_analisis): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_css_path_analisis; ?>">
    <?php endif; ?>
    <?php if ($include_custom_sidebar_fixed_css_analisis): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_fixed_css_path_analisis; ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo $base_css_path_analisis; ?>responsive.css">
    <link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/css/jquery.dataTables.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        /* --- MULAI CSS KUSTOM (DISALIN DARI DASHBOARD/INDEX.PHP REVISI V9 atau halaman lain yang sudah direvisi) --- */
        :root {
            --header-height: 60px; /* Dan seterusnya, salin semua variabel root dari revisi sebelumnya */
            --sidebar-width-normal: 250px; --sidebar-width-mini: 80px; --light-header-bg: #FFFFFF; --light-header-border: #E9EBF0; --light-content-bg: #F4F6F9; --light-text-primary: #343a40; --light-text-secondary: #6c757d; --light-icon-hover-bg: #f1f3f5; --sidebar-bg: #FFFFFF !important; --sidebar-header-gradient-start: #2ECC71 !important; --sidebar-header-gradient-end:rgb(39, 50, 174) !important; --sidebar-header-text-color: #FFFFFF !important; --sidebar-text-color: #4B5158 !important; --sidebar-text-hover-color:rgb(39, 50, 174) !important; --sidebar-hover-bg: #E9F7EF !important; --sidebar-active-bg: #D4EFDF !important; --sidebar-accent-color: rgb(39, 50, 174)  !important; --sidebar-accent-color-rgb: 39, 50, 174; --sidebar-border-color: #E0E4E8 !important; --card-bg: #FFFFFF; --card-shadow: 0 2px 5px rgba(0,0,0,0.07); --card-hover-shadow: 0 4px 10px rgba(0,0,0,0.1); --card-border-radius: 8px; --text-color-default: #495057; --text-color-muted: #6c757d; --action-card-encrypt-gradient: linear-gradient(135deg, #2ecc71, #27ae60); --action-card-decrypt-gradient: linear-gradient(135deg, #3498db, #2980b9); --action-card-analysis-gradient: linear-gradient(135deg, #9b59b6, #8e44ad); --action-card-text-color: #FFFFFF;
        }
        /* ... (Salin SEMUA CSS dari blok <style> halaman enkripsi.php yang sudah direvisi sebelumnya, termasuk semua @media query) ... */
        body { font-family: 'Roboto', sans-serif; font-size: 14px; background-color: var(--light-content-bg) !important; overflow-x: hidden; color: var(--text-color-default); } 
        .left-sidebar-pro { background-color: var(--sidebar-bg) !important; position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; width: var(--sidebar-width-normal) !important; z-index: 1032 !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; overflow: hidden; border-right: 1px solid var(--sidebar-border-color) !important; display: flex; flex-direction: column; }
        body.mini-navbar .left-sidebar-pro { width: var(--sidebar-width-mini) !important; }
        body.mini-navbar .sidebar-header .main-logo { display: none !important; }
        body.mini-navbar .sidebar-header strong { display: block !important; }
        body.mini-navbar .nalika-profile { display: none !important; }
        body.mini-navbar .metismenu li a span:not(.mini-click-non),
        body.mini-navbar .metismenu li a .pull-right-container { display: none !important; }
        body.mini-navbar .metismenu li a { text-align: center !important; padding: 12px 0 !important;}
        body.mini-navbar .metismenu li a .fa, body.mini-navbar .metismenu li a .nalika-icon { margin-right: 0 !important; font-size: 1.3em !important; }
        .header-top-area { background: var(--light-header-bg) !important; height: var(--header-height) !important; min-height: var(--header-height) !important; width: calc(100% - var(--sidebar-width-normal)) !important; display: flex !important; align-items: center !important; padding: 0 !important; box-sizing: border-box !important; position: fixed !important; top: 0 !important; left: var(--sidebar-width-normal) !important; z-index: 1030 !important; border-bottom: 1px solid var(--light-header-border) !important; box-shadow: var(--card-shadow) !important; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .all-content-wrapper { padding-top: var(--header-height) !important; margin-left: var(--sidebar-width-normal) !important; background: var(--light-content-bg) !important; min-height: calc(100vh - 56px); box-sizing: border-box; position: relative; transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow-x: hidden; padding-bottom: 70px; }
        body.mini-navbar .header-top-area { left: var(--sidebar-width-mini) !important; width: calc(100% - var(--sidebar-width-mini)) !important; }
        body.mini-navbar .all-content-wrapper { margin-left: var(--sidebar-width-mini) !important; }
        .header-top-wraper { width: 100% !important; height: 100% !important; padding: 0 20px !important; display: flex !important; align-items: center !important; justify-content: space-between !important; box-sizing: border-box !important; }
        .header-left-info { display: flex; align-items: center; flex-shrink: 0; }
        .menu-switcher-pro .navbar-btn { color: var(--light-text-secondary) !important; background-color: transparent !important; border: none !important; font-size: 1.5em !important; padding: 0 !important; margin-right: 15px !important; line-height: var(--header-height) !important; }
        .menu-switcher-pro .navbar-btn:hover, .menu-switcher-pro .navbar-btn:focus { color: var(--sidebar-accent-color) !important; }
        .dashboard-title-header { color: var(--light-text-primary) !important; margin: 0 !important; font-size: 1.25em !important; font-weight: 500 !important; line-height: var(--header-height) !important; white-space: nowrap; }
        .header-right-info { display: flex; align-items: center; justify-content: flex-end; flex-grow: 1; overflow: visible; }
        .header-right-info .navbar-nav { display: flex; align-items: center; padding-left: 0; margin-bottom: 0; }
        .header-right-info .navbar-nav > li { margin-left: 5px; list-style: none; } .header-right-info .navbar-nav > li:first-child { margin-left: 0; }
        .header-right-info .nav > li > a { color: var(--light-text-secondary) !important; padding: 7px !important; display: flex; align-items: center; border-radius: 50% !important; height: 34px !important; width: 34px !important; justify-content: center; transition: background-color 0.2s ease, color 0.2s ease; }
        .header-right-info .nav > li > a:hover, .header-right-info .nav > li > a:focus { color: var(--light-text-primary) !important; background-color: var(--light-icon-hover-bg) !important; }
        .header-right-info .nav > li > a > i { font-size: 1.1em !important; }
        .header-right-info .user-profile-area > a { padding: 5px 10px !important; height: auto !important; border-radius: 20px !important; width: auto !important; background-color: transparent !important; }
        .header-right-info .user-profile-area > a:hover, .header-right-info .user-profile-area > a:focus { background-color: var(--light-icon-hover-bg) !important; }
        .header-right-info .user-profile-area button { background-color: var(--sidebar-accent-color) !important; color: white !important; border: none !important; padding: 8px 15px !important; border-radius: 20px !important; font-size: 0.85em !important; font-weight: 500; transition: opacity 0.2s ease; }
        .header-right-info .user-profile-area button:hover { opacity: 0.85; }
        .author-log.dropdown-menu { right: 0px !important; left: auto !important; top: calc(100% + 10px) !important; box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; border: 1px solid var(--light-header-border) !important; border-radius: var(--card-border-radius) !important; margin-top: 0 !important; padding: 8px 0 !important; background-color: #fff !important; }
        .author-log.dropdown-menu > li > a { padding: 8px 18px !important; font-size: 0.9em !important; color: var(--light-text-primary) !important; display:flex; align-items:center; }
        .author-log.dropdown-menu > li > a:hover { background-color: var(--light-icon-hover-bg) !important; color: var(--sidebar-accent-color) !important; }
        .author-log.dropdown-menu > li > a .fa { color: var(--light-text-secondary) !important; margin-right: 10px; width:16px; text-align:center; }
        .author-log.dropdown-menu > li > a:hover .fa { color: var(--sidebar-accent-color) !important; }
        .author-log.dropdown-menu .divider { margin: 6px 0; background-color: var(--light-header-border); }
        .sidebar-header { padding: 0 !important; height: auto !important; min-height: calc(var(--header-height) + 70px) !important; background: var(--sidebar-header-gradient-start); background: linear-gradient(135deg, var(--sidebar-header-gradient-end), var(--sidebar-header-gradient-start)) !important; text-align: center !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border-bottom: none !important; color: var(--sidebar-header-text-color) !important; }
        .sidebar-header > a { display: block !important; line-height: normal !important; margin-top: 15px !important; padding: 0 !important; border: none !important; outline: none !important; box-shadow: none !important; text-decoration: none !important; }
        .sidebar-header .main-logo { max-width: 150px !important; max-height: 46px !important; height: auto !important; display: block !important; object-fit: contain !important; margin: 0 auto 10px auto !important; filter: brightness(0) invert(1); }
        .sidebar-header strong { display: none; } .sidebar-header strong img { max-height: 30px; filter: brightness(0) invert(1); }
        .nalika-profile { padding: 0 15px 15px 15px !important; text-align: center !important; border-bottom: 1px solid var(--sidebar-border-color) !important; background: var(--sidebar-bg) !important; }
        .nalika-profile .profile-dtl { padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2); }
        .nalika-profile .profile-dtl img.profile-img-sidebar { width: 60px !important; height: 60px !important; border-radius: 50% !important; margin-bottom: 10px !important; border: 2px solid var(--sidebar-accent-color) !important; object-fit: cover; }
        .nalika-profile .profile-dtl h2 { color: var(--light-text-primary) !important; font-size: 0.95em !important; margin-bottom: 3px !important; font-weight: 500 !important; }
        .nalika-profile .profile-dtl .designation { font-size: 0.8em !important; color: var(--light-text-secondary) !important; display: block; }
        .left-custom-menu-adp-wrap { flex-grow: 1; overflow-y: auto; background-color: var(--sidebar-bg) !important; }
        .metismenu { background-color: var(--sidebar-bg) !important; padding-top:10px; padding-bottom: 20px; } .metismenu li { background-color: var(--sidebar-bg) !important; }
        .metismenu li a { color: var(--sidebar-text-color) !important; padding: 12px 20px !important; font-size: 0.9em !important; border-bottom: none !important; display: flex; align-items: center; transition: background-color 0.2s ease, color 0.2s ease, border-left-color 0.2s ease; }
        .metismenu li:last-child a { border-bottom: none !important; }
        .metismenu li a:hover, .metismenu li.active > a { background-color: var(--sidebar-hover-bg) !important; color: var(--sidebar-text-hover-color) !important; border-left: 4px solid var(--sidebar-accent-color) !important; padding-left: 16px !important; }
        .metismenu li.active > a { background-color: var(--sidebar-active-bg) !important; font-weight: 500; }
        .metismenu li a .fa, .metismenu li a .nalika-icon { margin-right: 12px !important; font-size: 1.05em !important; width: 20px; text-align: center; flex-shrink: 0; color: var(--sidebar-text-color) !important; transition: color 0.2s ease; }
        .metismenu li a:hover .fa, .metismenu li a:hover .nalika-icon, .metismenu li.active > a .fa, .metismenu li.active > a .nalika-icon { color: var(--sidebar-text-hover-color) !important; }
        .metismenu ul { border-left: 4px solid var(--sidebar-border-color, #E0E4E8); margin-left: 18px; padding-left: 0; }
        .metismenu ul a { padding-left: 20px !important; font-size: 0.85em !important; background-color: #fdfdfd !important; border-bottom-style: dashed !important; border-bottom-color: #f0f0f0 !important; }
        .metismenu ul a:hover, .metismenu ul li.active > a { background-color: var(--sidebar-hover-bg) !important; padding-left: 16px !important; }
        .content-wrap { padding: 20px 15px; }
        .breadcome-area-custom { background-color: transparent; padding: 15px 0px; margin-bottom: 0px; border-bottom: 1px solid var(--light-header-border); margin-left: -15px; margin-right: -15px; padding-left: 15px; padding-right: 15px; margin-top: -20px; margin-bottom: 20px; }
        .breadcome-list-custom { padding: 0; margin: 0; list-style: none; display: flex; align-items: center; font-size: 0.9em; }
        .breadcome-list-custom li a { color: var(--sidebar-accent-color); text-decoration: none; transition: color 0.2s ease; }
        .breadcome-list-custom li a:hover { color: var(--light-text-primary); }
        .breadcome-list-custom li .bread-slash { margin: 0 10px; color: var(--light-text-secondary); }
        .breadcome-list-custom li.active { color: var(--light-text-primary); font-weight: 500; }
        .footer-copyright-area { background: var(--card-bg, #fff) !important; padding: 18px 0 !important; border-top: 1px solid var(--light-header-border) !important; position: fixed; bottom: 0; width: calc(100% - var(--sidebar-width-normal)); left: var(--sidebar-width-normal); z-index: 1000; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        body.mini-navbar .footer-copyright-area { left: var(--sidebar-width-mini) !important; width: calc(100% - var(--sidebar-width-mini)) !important; }
        .footer-copy-right p { color: var(--text-color-muted) !important; font-size: 0.85em; margin-bottom:0; text-align: center; }
        @media (max-width: 991px) { .header-right-info .nav > li.d-none.d-md-flex { display: none !important; } .header-right-info .user-profile-details { display: none !important; } .header-right-info .user-profile-area img.profile-img-header { margin-right:0; } .dashboard-title-header { font-size: 1.1em !important; } }
        @media (max-width: 767px) { .dashboard-title-header { font-size: 1em !important; white-space: normal; max-width: 120px; overflow: hidden; text-overflow: ellipsis; } .header-top-wraper { padding: 0 10px !important; } .header-right-info .navbar-nav > li { margin-left: 2px !important; } .header-right-info .nav > li > a { padding: 8px 5px !important; height: 34px !important; width: 34px !important; } .header-right-info .nav > li > a > i { font-size: 1.05em !important; } .menu-switcher-pro .navbar-btn { margin-right: 8px !important;} .footer-copyright-area { width: 100% !important; left: 0 !important; } .all-content-wrapper { margin-left: 0 !important; } body:not(.mini-navbar) .left-sidebar-pro { left: -250px !important; z-index: 1035; } body.mini-navbar .left-sidebar-pro { left: 0 !important; width: var(--sidebar-width-normal) !important; } body.mini-navbar .header-top-area, body.mini-navbar .all-content-wrapper, body.mini-navbar .footer-copyright-area { margin-left: 0 !important; left: 0 !important; width: 100% !important; } }
        @media (max-width: 480px) { .dashboard-title-header { font-size: 0.95em !important; max-width:100px; } .header-right-info .user-profile-area button { padding: 6px 10px !important; font-size: 0.8em !important; } }
        /* --- AKHIR CSS KUSTOM UMUM --- */

        /* --- CSS SPESIFIK UNTUK HALAMAN ANALISIS (Mirip dekripsi.php) --- */
        .table-container-card { background-color: var(--card-bg); padding: 25px 30px; border-radius: var(--card-border-radius); box-shadow: var(--card-shadow); margin-top: 0; }
        .table-container-card h2.table-title, .table-container-card h4.table-title { color: var(--light-text-primary); margin-top: 0; margin-bottom: 10px; font-size: 1.5em; font-weight: 500; }
        .table-container-card h4.table-title .fa { margin-right: 8px; color: var(--sidebar-accent-color); }
        .table-container-card p.table-subtitle { color: var(--text-color-muted); font-size:0.9em; margin-bottom:25px; border-bottom: 1px solid var(--light-header-border); padding-bottom: 15px; }
        .table thead th { background-color: #f8f9fa; color: var(--light-text-primary); font-weight: 500; border-bottom-width: 2px; border-color: var(--light-header-border); font-size:0.8em; text-transform: uppercase; padding: 10px 12px; text-align: left; } /* Dibuat lebih kecil font-size dan align left */
        .table tbody tr:hover { background-color: #f1f3f5; }
        .table td, .table th { vertical-align: middle; font-size: 0.88em; padding: 9px 12px; color: var(--text-color-default); text-align: left; } /* Dibuat lebih kecil font-size dan align left */
        .table td.hash-cell { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.85em; }
        .table .badge { font-weight: 500; font-size: 0.8em; padding: 0.4em 0.6em;} /* Ukuran badge disamakan */
        .table .badge-alg.aes128 { background-color: #17a2b8; color:white; } 
        .table .badge-alg.aes256 { background-color: #28a745; color:white; } 
        .table .badge-op.enkripsi { background-color: #ffc107; color:#212529; } 
        .table .badge-op.dekripsi { background-color: #6f42c1; color:white;} 
        .table .badge-op.unknown, .table .badge-op.kosong { background-color: #6c757d; color:white;} 
        /* DataTables styling (sama seperti dekripsi.php) */
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { font-size: 0.88em; color: var(--text-color-muted); margin-bottom: 0.5rem; }
        .dataTables_wrapper .dataTables_length select { border: 1px solid #ddd; border-radius: 4px; padding: 4px 8px; background-color: #fff; margin: 0 5px; }
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #ddd; border-radius: 4px; padding: 5px 8px; margin-left: 5px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.4em 0.8em; margin-left: 2px; border-radius: 4px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: var(--sidebar-accent-color) !important; color: white !important; border-color: var(--sidebar-accent-color) !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #e9ecef !important; border-color: #ddd !important; color: var(--light-text-primary) !important; }
        .dataTables_wrapper .row:first-child > div { margin-bottom: 0.5rem; } /* Jarak antara filter/length dan tabel */

        /* --- AKHIR CSS SPESIFIK --- */
    </style>
</head>
<body class="">

    <div class="left-sidebar-pro">
        <nav id="sidebar" class="">
            <div class="sidebar-header">
                <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo Aplikasi" /></a>
                <strong><img src="img/logo/logosn.png" alt="Logo Mini" /></strong>
            </div>
            <div class="nalika-profile">
                <div class="profile-dtl">
                    <h2><?php echo htmlspecialchars($data_user['fullname']); ?> <span class="designation icon"><?php echo htmlspecialchars($data_user['job_title']); ?></span></h2>
                </div>
            </div>
            <div class="left-custom-menu-adp-wrap comment-scrollbar">
                <nav class="sidebar-nav left-sidebar-menu-pro">
                    <?php include('sidebar-nav-universal.php'); ?>
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
                    <h1 class="dashboard-title-header">Analisis Kinerja AES</h1>
                </div>
                <div class="header-right-info">
                    <ul class="nav navbar-nav mai-top-nav header-right-menu">
                        <li class="nav-item d-none d-md-flex">
                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><i class="nalika-search" aria-hidden="true"></i></a>
                            <div role="menu" class="dropdown-menu search-ml animated zoomIn">
                                <div class="search-active-menu"><form action="#"><input type="text" placeholder="Cari disini..." class="form-control"><a href="#"><i class="fa fa-search"></i></a></form></div>
                            </div>
                        </li>
                        <li class="nav-item user-profile-area">
                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                <button>Logout</button>
                            </a>
                            <ul role="menu" class="dropdown-header-top author-log dropdown-menu animated zoomIn">
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
                                <li class="active">Analisis Kinerja AES</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-container-card">
                            <h4 class="table-title"><i class="fa fa-bar-chart"></i> Analisis AES-128 dan AES-256</h4>
                            <p class="table-subtitle">
                                Data berikut menampilkan perbandingan kinerja antar algoritma AES-128 dan AES-256 berdasarkan operasi enkripsi dan dekripsi yang telah dilakukan.
                            </p>
                            <div class="table-responsive">
                                <table class="table table-hover" id="analisisAesTable"> 
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama File</th>
                                            <th>Algoritma</th>
                                            <th>Ukuran (KB)</th>
                                            <th>Waktu (ms)</th>
                                            <th>Operasi</th>
                                            <th>Iterasi KDF</th>
                                            <th>Salt (Hex)</th>
                                            <th>IV (Hex)</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Using your original query and loop structure
                                        $query_analisis = mysqli_query($connect, "SELECT * FROM file WHERE alg_used IS NOT NULL ORDER BY tgl_upload DESC");
                                        $no_analisis = 1;
                                        if ($query_analisis && mysqli_num_rows($query_analisis) > 0) {
                                            while ($row_analisis = mysqli_fetch_assoc($query_analisis)) {
                                                // Using your exact logic for badge styling
                                                $alg_class_badge = strtolower(str_replace('-', '', $row_analisis['alg_used']));
                                                $op_text = !empty($row_analisis['operation_type']) ? ucfirst(htmlspecialchars($row_analisis['operation_type'])) : '-';
                                                $op_class_temp = !empty($row_analisis['operation_type']) ? str_replace(' (simulasi)', '', $row_analisis['operation_type']) : 'kosong';
                                                $op_class_badge = strtolower(htmlspecialchars($op_class_temp));

                                                // Using the echo format as you requested, with the new columns
                                                echo "<tr>
                                                    <td>" . $no_analisis++ . "</td>
                                                    <td>" . htmlspecialchars($row_analisis['file_name_source']) . "</td>
                                                    <td><span class='badge badge-alg " . $alg_class_badge . "'>" . htmlspecialchars($row_analisis['alg_used']) . "</span></td>
                                                    <td>" . htmlspecialchars(number_format((float)$row_analisis['file_size_kb'], 2)) . "</td>
                                                    <td>" . htmlspecialchars($row_analisis['process_time_ms']) . "</td>
                                                    <td><span class='badge badge-op " . $op_class_badge . "'>" . $op_text . "</span></td>
                                                    <td>" . htmlspecialchars(number_format($row_analisis['kdf_iterations'])) . "</td>
                                                    <td class='hex-cell' title='" . htmlspecialchars($row_analisis['password_salt_hex']) . "'>" . substr(htmlspecialchars($row_analisis['password_salt_hex']), 0, 12) . "...</td>
                                                    <td class='hex-cell' title='" . htmlspecialchars($row_analisis['file_iv_hex']) . "'>" . substr(htmlspecialchars($row_analisis['file_iv_hex']), 0, 12) . "...</td>
                                                    <td>" . htmlspecialchars(date('d M Y, H:i', strtotime($row_analisis['tgl_upload']))) . "</td>
                                                </tr>";
                                            }
                                        } else {
                                            if(!$query_analisis) {
                                                // Corrected colspan for the new number of columns
                                                echo "<tr><td colspan='10' class='text-center text-danger'>Error query: " . mysqli_error($connect) . "</td></tr>";
                                            } else {
                                                echo "<tr><td colspan='10' class='text-center text-muted'>Belum ada data analisis AES yang relevan.</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-copyright-area">
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
    </div>

    <script src="js/vendor/jquery-1.12.4.min.js"></script>
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
            // Inisialisasi DataTables untuk tabel analisis
            if ($('#analisisAesTable tbody tr').length > 1 || ($('#analisisAesTable tbody tr').length === 1 && !$('#analisisAesTable tbody td[colspan]').length) ) {
                 $('#analisisAesTable').DataTable({
                    "language": { "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                    "pageLength": 10,
                    "responsive": true,
                    "order": [[ 0, "asc" ]] // Default order by nomor
                });
            }
            
            // Fungsi adjustMainLayout yang sudah ada
            function adjustMainLayout() {
                var sidebarPro = $('.left-sidebar-pro'); var sidebarWidth = 0; var rootStyles = getComputedStyle(document.documentElement);
                var defaultSidebarNormalWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-normal').trim()) || 250;
                var defaultSidebarMiniWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-mini').trim()) || 80;
                var headerHeight = parseFloat(rootStyles.getPropertyValue('--header-height').trim()) || 60;
                var footerArea = $('.footer-copyright-area'); var footerHeight = (footerArea.length > 0 && footerArea.css('position') === 'fixed') ? (footerArea.outerHeight() || 56) : 0;
                if ($(window).width() >= 768) { if (sidebarPro.length > 0 && sidebarPro.is(':visible')) { if ($('body').hasClass('mini-navbar')) { sidebarWidth = defaultSidebarMiniWidth; } else { sidebarWidth = defaultSidebarNormalWidth; } } } else { sidebarWidth = 0; }
                var headerTopArea = $('.header-top-area'); var allContentWrapper = $('.all-content-wrapper');
                if (headerTopArea.css('position') === 'fixed') { if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) { headerTopArea.css({ 'left': sidebarWidth + 'px', 'width': 'calc(100% - ' + sidebarWidth + 'px)' }); } else { headerTopArea.css({ 'left': '0px', 'width': '100%'}); } }
                if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) { allContentWrapper.css({ 'margin-left': sidebarWidth + 'px', 'padding-top': headerHeight + 'px', 'padding-bottom': (footerHeight + 20) + 'px' }); } else { allContentWrapper.css({ 'margin-left': '0px', 'padding-top': headerHeight + 'px', 'padding-bottom': (footerHeight + 20) + 'px' }); }
                if (footerArea.length > 0 && footerArea.css('position') === 'fixed') { if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) { footerArea.css({ 'left': sidebarWidth + 'px', 'width': 'calc(100% - ' + sidebarWidth + 'px)' }); } else { footerArea.css({ 'left': '0px', 'width': '100%'}); } }
            }
            adjustMainLayout();
            var bodyNode = document.querySelector('body');
            if (bodyNode) { var observer = new MutationObserver(function(mutationsList) { for(let mutation of mutationsList) { if (mutation.type === 'attributes' && mutation.attributeName === 'class') { setTimeout(adjustMainLayout, 50); if ($(window).width() < 768) { if ($('body').hasClass('mini-navbar')) { $('#sidebarCollapse').addClass('active'); } else { $('#sidebarCollapse').removeClass('active'); } } break; } } }); observer.observe(bodyNode, { attributes: true }); }
            $(window).on('resize', function() { setTimeout(adjustMainLayout, 50); });
            $('#sidebarCollapse').on('click', function () { if ($(window).width() < 768) { $('body').toggleClass('mini-navbar'); } });
        });
    </script>
</body>
</html>