<?php
session_start();
// Pastikan path ke config.php benar dari direktori dashboard
if (file_exists('../config.php')) {
    include('../config.php');
} else {
    if (file_exists('config.php')) {
        include('config.php');
    } else {
        die("File config.php tidak ditemukan. Pastikan path sudah benar.");
    }
}

if (empty($_SESSION['username'])) {
    header("location:../index.php");
    exit;
}

$user = $_SESSION['username'];
$stmt_update = mysqli_prepare($connect, "UPDATE users SET last_activity=now() WHERE username=?");
if ($stmt_update) {
    mysqli_stmt_bind_param($stmt_update, "s", $user);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}

$data_user = ['fullname' => 'User', 'job_title' => 'Role']; 
$stmt_user_details = mysqli_prepare($connect, "SELECT fullname, job_title FROM users WHERE username=?");
if ($stmt_user_details) {
    mysqli_stmt_bind_param($stmt_user_details, "s", $user);
    mysqli_stmt_execute($stmt_user_details);
    $result_user_details = mysqli_stmt_get_result($stmt_user_details);
    $fetched_data_user = mysqli_fetch_array($result_user_details);
    if ($fetched_data_user) {
        $data_user = $fetched_data_user;
    }
    mysqli_stmt_close($stmt_user_details);
}

$total_users = $encrypted_files = $decrypted_files = $total_files = 0; 
$queries = [
    "total_users" => "SELECT COUNT(*) as total FROM users",
    "encrypted_files" => "SELECT COUNT(*) as total FROM file WHERE status='1'",
    "decrypted_files" => "SELECT COUNT(*) as total FROM file WHERE status='2'",
    "total_files" => "SELECT COUNT(*) as total FROM file"
];
foreach ($queries as $key => $sql) {
    $q_result = mysqli_query($connect, $sql);
    if ($q_result) {
        $data_stat = mysqli_fetch_array($q_result);
        $$key = $data_stat['total'] ?? 0;
    }
}

$base_css_path = 'css/';
$custom_sidebar_css_path = $base_css_path . 'custom-style-sidebar.css';
$custom_sidebar_fixed_css_path = $base_css_path . 'custom-style-sidebar-fixed.css';
$include_custom_sidebar_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_css_path);
$include_custom_sidebar_fixed_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_fixed_css_path);

// Warna untuk kartu aksi dari screenshot image_b2f92c.png & image_b2f50e.png
$encrypt_card_color = "#28a745"; // Hijau (Mirip tombol Go to Encrypt di screenshot lama, tapi bisa disesuaikan)
$decrypt_card_color = "#007bff"; // Biru (Mirip tombol Go to Decrypt di screenshot lama)
$analysis_card_color = "#6f42c1"; // Ungu (Mirip tombol Go to Analysis di screenshot lama)

// Dari image_b2f50e.png & image_b2f92c.png:
$action_card_encrypt_bg = "linear-gradient(135deg, #2ecc71, #27ae60)"; // Gradasi Hijau
$action_card_decrypt_bg = "linear-gradient(135deg, #3498db, #2980b9)"; // Gradasi Biru
$action_card_analysis_bg = "linear-gradient(135deg, #9b59b6, #8e44ad)";// Gradasi Ungu

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Kriptografi AES</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/nalika-icon.css">
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <link rel="stylesheet" href="css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="css/metisMenu/metisMenu-vertical.css">
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="style.css"> 

    <?php if ($include_custom_sidebar_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_css_path; ?>">
    <?php endif; ?>
    <?php if ($include_custom_sidebar_fixed_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_fixed_css_path; ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="css/responsive.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        /* --- MULAI CSS KUSTOM (REVISI V9 - Colorful Action Cards, Light Sidebar Gradasi) --- */
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
            --sidebar-header-gradient-end:rgb(39, 50, 174) !important;   /* Hijau Tua (sesuai screenshot) */
            --sidebar-header-text-color: #FFFFFF !important;
            --sidebar-text-color: #4B5158 !important; /* Teks menu gelap di sidebar terang */
            --sidebar-text-hover-color:rgb(39, 50, 174) !important; /* Warna aksen untuk hover */
            --sidebar-hover-bg: #E9F7EF !important;
            --sidebar-active-bg: #D4EFDF !important; 
            --sidebar-accent-color: rgb(39, 50, 174)  !important;
            --sidebar-border-color: #E0E4E8 !important;

            --card-bg: #FFFFFF;
            --card-shadow: 0 2px 5px rgba(0,0,0,0.07);
            --card-hover-shadow: 0 4px 10px rgba(0,0,0,0.1);
            --card-border-radius: 8px;

            --text-color-default: #495057;
            --text-color-muted: #6c757d;

            /* Warna untuk Kartu Aksi Colorful (sesuai image_b2f50e.png & b2f92c.png) */
            --action-card-encrypt-gradient: <?php echo $action_card_encrypt_bg; ?>;
            --action-card-decrypt-gradient: <?php echo $action_card_decrypt_bg; ?>;
            --action-card-analysis-gradient: <?php echo $action_card_analysis_bg; ?>;
            --action-card-text-color: #FFFFFF;
        }

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            background-color: var(--light-content-bg) !important;
            overflow-x: hidden; 
        }

        /* 1. HEADER & SIDEBAR FIXED LAYOUT */
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
        body.mini-navbar .nalika-profile { display: none !important; }
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
            min-height: 100vh; box-sizing: border-box; position: relative;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden; 
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
        .header-right-info .nav > li > a {
            color: var(--light-text-secondary) !important; padding: 7px !important; display: flex; align-items: center;
            border-radius: 50% !important; height: 34px !important; width: 34px !important;
            justify-content: center; transition: background-color 0.2s ease, color 0.2s ease;
        }
        .header-right-info .nav > li > a:hover, .header-right-info .nav > li > a:focus { 
            color: var(--light-text-primary) !important; 
            background-color: var(--light-icon-hover-bg) !important;
        }
        .header-right-info .nav > li > a > i { font-size: 1.1em !important; }

        .header-right-info .user-profile-area { position: relative; margin-left: 8px !important; }
        .header-right-info .user-profile-area > a { 
            padding: 5px 10px !important; height: auto !important; border-radius: 20px !important;
            width: auto !important; 
        }
        .header-right-info .user-profile-area > a:hover, .header-right-info .user-profile-area > a:focus {
             background-color: var(--light-icon-hover-bg) !important;
        }
        .header-right-info .user-profile-area img { width: 26px; height: 26px; border-radius: 50%; margin-right: 7px; }
        .user-profile-details { display: flex; flex-direction: column; justify-content: center; text-align: left; line-height: 1.2; }
        .header-right-info .user-profile-area .admin-name { font-weight: 500 !important; font-size: 0.82em !important; color: var(--light-text-primary) !important; white-space: nowrap; }
        .header-right-info .user-profile-area .admin-title-header { font-size: 0.7em !important; color: var(--light-text-secondary) !important; white-space: nowrap; }
        .header-right-info .user-profile-area .fa-angle-down { margin-left: 6px !important; font-size: 0.8em !important; color: var(--light-text-secondary) !important; }
        
        .author-log.dropdown-menu {
            right: 0px !important; left: auto !important;
            top: calc(100% + 5px) !important; 
            box-shadow: 0 3px 8px rgba(0,0,0,0.12) !important; 
            border: 1px solid #ddd !important; 
            border-radius: var(--card-border-radius) !important; margin-top: 0 !important;
            padding: 5px 0 !important; background-color: #fff !important; 
        }
        .author-log.dropdown-menu > li > a { padding: 7px 15px !important; font-size: 0.88em !important; color: var(--light-text-primary) !important; }
        .author-log.dropdown-menu > li > a:hover { background-color: #f5f5f5 !important; color: var(--light-text-primary) !important; }
        .author-log.dropdown-menu > li > a .fa { color: var(--light-text-secondary) !important; margin-right: 8px; }
        .author-log.dropdown-menu > li > a:hover .fa { color: var(--sidebar-accent-color) !important; }


        /* SIDEBAR STYLING (TERANG DENGAN HEADER GRADASI) */
        .sidebar-header { /* Area Logo & Profil di Sidebar */
            padding: 0 !important; /* Padding diatur oleh anak elemen */
            height: auto !important; /* Tinggi menyesuaikan konten logo dan profil */
            min-height: calc(var(--header-height) + 70px) !important; /* Estimasi tinggi logo + profil area */
            background: var(--sidebar-header-gradient-start); /* Fallback jika gradasi tidak didukung */
            background: linear-gradient(135deg, var(--sidebar-header-gradient-end), var(--sidebar-header-gradient-start)) !important; /* Gradasi */
            text-align: center !important; display: flex !important; flex-direction: column !important;
            align-items: center !important; justify-content: center !important; box-sizing: border-box !important;
            border-bottom: none !important; /* Hilangkan border bawah default jika ada */
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
            margin: 0 auto 10px auto !important; /* Margin bawah logo */
            filter: brightness(0) invert(1); /* Membuat logo putih jika aslinya gelap, sesuaikan */
        }
        .sidebar-header strong { display: none; } 
        .sidebar-header strong img { max-height: 30px; filter: brightness(0) invert(1); }

        /* Profil di Sidebar (menyatu dengan header sidebar gradasi) */
        .nalika-profile {
            padding: 0 15px 15px 15px !important; /* Padding bawah saja */
            text-align: center !important;
            border-bottom: 1px solid var(--sidebar-border-color) !important; /* Border setelah area header sidebar */
            background: var(--sidebar-bg) !important; /* Latar putih untuk area menu */
        }
        .nalika-profile .profile-dtl {
            padding-top: 15px; /* Jarak dari header gradasi ke foto profil */
            border-top: 1px solid rgba(255,255,255,0.2); /* Garis pemisah halus di atas foto profil */
        }
        .nalika-profile .profile-dtl img {
            width: 50px !important; height: 50px !important; border-radius: 50% !important;
            margin-bottom: 8px !important; border: 2px solid var(--sidebar-accent-color) !important;
        }
        .nalika-profile .profile-dtl h2 { /* Nama User di Sidebar */
            color: var(--light-text-primary) !important; font-size: 0.9em !important;
            margin-bottom: 2px !important; font-weight: 500 !important;
        }
        .nalika-profile .profile-dtl h2 .designation { /* Jabatan di Sidebar */
            font-size: 0.75em !important; color: var(--light-text-secondary) !important;
        }

        /* Menu Sidebar (Latar Terang) */
        .left-custom-menu-adp-wrap { /* Wrapper untuk menu scrollable */
            flex-grow: 1;
            overflow-y: auto;
            background-color: var(--sidebar-bg) !important; /* Pastikan wrapper menu juga terang */
        }
        .metismenu { background-color: var(--sidebar-bg) !important; padding-bottom: 20px; }
        .metismenu li { background-color: var(--sidebar-bg) !important; }
        .metismenu li a {
            color: var(--sidebar-text-color) !important;
            padding: 10px 20px !important; 
            font-size: 0.88em !important;
            border-bottom: 1px solid var(--sidebar-border-color) !important;
            display: flex; align-items: center;
        }
        .metismenu li:last-child a { border-bottom: none !important; }
        .metismenu li a:hover,
        .metismenu li.active > a { 
            background-color: var(--sidebar-hover-bg) !important;
            color: var(--sidebar-text-hover-color) !important; 
            border-left: 3px solid var(--sidebar-accent-color) !important; 
            padding-left: 17px !important;
        }
        .metismenu li.active > a { background-color: var(--sidebar-active-bg) !important; }
        .metismenu li a .fa, .metismenu li a .nalika-icon { 
            margin-right: 10px !important; font-size: 1em !important; 
            width: 18px; text-align: center; flex-shrink: 0;
            color: var(--sidebar-text-color) !important; /* Warna ikon menu */
        }
        .metismenu li a:hover .fa, .metismenu li a:hover .nalika-icon,
        .metismenu li.active > a .fa, .metismenu li.active > a .nalika-icon {
            color: var(--sidebar-text-hover-color) !important; 
        }
        .metismenu ul a { padding-left: 38px !important; background-color: rgba(0,0,0,0.02) !important; border-bottom-style: dashed !important; }
        .metismenu ul a:hover { background-color: var(--sidebar-hover-bg) !important; }


        /* KONTEN AREA */
        .welcome-alert-container { margin: 20px 15px 0 15px !important; }
        .alert.alert-success { 
            background-color: #e7f5ff !important; color: #0056b3 !important; 
            border: 1px solid #b8daff !important; border-left: 4px solid #007bff !important; 
            border-radius: var(--card-border-radius) !important; box-shadow: var(--card-shadow) !important;
        }
        .alert.alert-success hr { border-top-color: #a3cfff !important; margin-top: 0.8rem; margin-bottom: 0.8rem; }
        .alert.alert-success .alert-heading { color: #004085 !important; font-weight:500; font-size: 1.05em;}
        .alert.alert-success p { font-size: 0.88em; }

        .product-sales-area.mg-tb-30 { margin: 20px 0 !important; padding-left: 0; padding-right: 0;}
        .product-sales-area.mg-tb-30 .container-fluid { padding-left: 15px; padding-right: 15px;}
        
        .stat-card { 
            background-color: var(--card-bg) !important; border-radius: var(--card-border-radius) !important; 
            padding: 18px !important; margin-bottom: 20px !important; 
            box-shadow: var(--card-shadow) !important;
            transition: all .2s ease-in-out; height: 100%; 
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--card-hover-shadow) !important; }
        .stat-card .stat-icon { font-size: 2.1em; margin-bottom: 10px; } 
        .stat-card h4 { font-size: 0.9em; margin-bottom: 5px; color: var(--text-color-muted); font-weight: 500; text-transform: uppercase; }
        .stat-card p.stat-number { font-size: 1.6em; font-weight: 600; margin-bottom: 0; } 
        
        /* KARTU AKSI COLORFUL & ELEGAN (Encrypt, Decrypt, Analysis) */
        .action-card {
            border-radius: var(--card-border-radius) !important;
            padding: 20px !important;
            margin-bottom: 25px !important;
            color: var(--action-card-text-color) !important;
            display: flex !important; 
            align-items: center !important;
            transition: all .25s ease-in-out !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12); /* Shadow lebih menonjol */
            position: relative; /* Untuk positioning elemen absolut di dalam jika perlu */
            overflow: hidden; /* Untuk efek gradasi yang rapi */
        }
        .action-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
        }
        .action-card .action-icon-container { /* Wadah untuk ikon agar bisa diberi background/styling */
            width: 80px; /* Lebar area ikon */
            height: 100%; /* Tinggi penuh kartu (jika kartu tidak terlalu tinggi) atau tinggi tetap */
            min-height: 80px; /* Tinggi minimal area ikon */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            /* background-color: rgba(255,255,255,0.1); /* Latar soft untuk area ikon */
            /* border-radius: var(--card-border-radius) 0 0 var(--card-border-radius); /* Rounded di sisi kiri */
        }
        .action-card .action-icon {
            font-size: 2.5em; 
            opacity: 0.9;
        }
        .action-card .action-content {
            flex-grow: 1;
            position: relative; /* Untuk z-index jika ada elemen overlay */
            z-index: 2;
        }
        .action-card h3 {
            margin-top: 0;
            margin-bottom: 10px; 
            color: var(--action-card-text-color) !important;
            font-size: 1.35em; 
            font-weight: 500;
        }
        .action-card p {
            color: rgba(255,255,255,0.9) !important; 
            font-size: 0.88em;
            line-height: 1.55;
            margin-bottom: 18px; 
        }
        .action-card .btn-action-card { /* Class khusus untuk tombol di kartu aksi */
            padding: 9px 22px !important; 
            font-size: 0.88em !important;
            border-radius: 20px !important; 
            background-color: var(--action-card-text-color) !important; /* Tombol putih */
            color: #333 !important; /* Teks tombol gelap */
            border: none !important;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .action-card .btn-action-card:hover {
            background-color: #f8f9fa !important; /* Hover tombol sedikit abu-abu */
            color: #222 !important;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        .action-card .btn-action-card .fa { margin-right: 7px; }
        
        .action-card.card-encrypt { background: var(--action-card-encrypt-gradient) !important; }
        .action-card.card-decrypt { background: var(--action-card-decrypt-gradient) !important; }
        .action-card.card-analysis { background: var(--action-card-analysis-gradient) !important; }
        .action-card hr { display: none !important; }


        .footer-copyright-area { background: #fff !important; padding: 18px 0 !important; border-top: 1px solid var(--light-header-border) !important; }
        .footer-copy-right p { color: var(--text-color-muted) !important; font-size: 0.85em; }

        /* RESPONSIVITAS */
        @media (max-width: 991px) { 
            .header-right-info .nav > li.d-none.d-md-flex { display: none !important; } 
            .dashboard-title-header { font-size: 1.2em !important; }
            .action-card { flex-direction: column; align-items: flex-start; text-align: center;} 
            .action-card .action-icon-container { margin-right: 0; margin-bottom: 15px; width: 100%; justify-content: center; }
            .action-card .action-content { text-align: center; width: 100%;}
        }
        @media (max-width: 767px) { 
            .dashboard-title-header { font-size: 1.1em !important; }
            .header-top-wraper { padding: 0 10px !important; }
            .header-right-info .navbar-nav > li { margin-left: 2px !important; }
            .header-right-info .nav > li > a { padding: 8px 5px !important; height: 34px !important; width: 34px !important; }
            .header-right-info .nav > li > a > i { font-size: 1.05em !important; }
            .menu-switcher-pro .navbar-btn { margin-right: 8px !important;}
            .action-card h3 { font-size: 1.15em; }
            .action-card p { font-size: 0.82em; }
            .action-card .btn-action-card { font-size: 0.85em; padding: 7px 15px !important; }
        }
         @media (max-width: 480px) {
            .header-right-info .user-profile-details .admin-name,
            .header-right-info .user-profile-details .admin-title-header { display: none !important; }
            .header-right-info .user-profile-area img { margin-right: 0 !important; }
            .header-right-info .user-profile-area .fa-angle-down { margin-left: 4px !important; }
            .dashboard-title-header { font-size: 1em !important; }
        }
        /* --- AKHIR CSS KUSTOM --- */
    </style>
</head>
<body>

<div class="left-sidebar-pro">
    <nav id="sidebar" class="">
        <div class="sidebar-header"> <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo PALW" /></a>
            <strong><img src="img/logo/logosn.png" alt="Logo Small PALW" /></strong>
        </div>
        <div class="nalika-profile"> <div class="profile-dtl">
                <h2><?php echo htmlspecialchars($data_user['fullname']); ?></h2>
                <p class="designation icon"><?php echo htmlspecialchars($data_user['job_title']); ?></p>
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
                <h1 class="dashboard-title-header">Dashboard</h1>
            </div>
            <div class="header-right-info">
                <ul class="nav navbar-nav mai-top-nav header-right-menu">
                    <li class="nav-item d-none d-md-flex"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><i class="nalika-search" aria-hidden="true"></i></a>
                        <div role="menu" class="dropdown-menu search- мл animated zoomIn">
                            <div class="search-active-menu"><form action="#"><input type="text" placeholder="Search here..." class="form-control"><a href="#"><i class="fa fa-search"></i></a></form></div>
                        </div>
                    </li>
                    <li class="nav-item dropdown d-none"> {/* Ikon Pesan DISembunyikan Sementara*/}
                        <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><i class="fa fa-envelope-o" aria-hidden="true"></i><span class="indicator-ms"></span></a>
                    </li>
                    <li class="nav-item dropdown d-none"> {/* Ikon Pesan DISembunyikan Sementara*/}
                        <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><i class="fa fa-bell-o" aria-hidden="true"></i><span class="indicator-nt"></span></a>
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

    <div class="welcome-alert-container">
         <div class="alert alert-success" role="alert" style="margin-bottom: 0; border-radius: var(--card-border-radius);">
            <h4 class="alert-heading"><?php echo htmlspecialchars($data_user['fullname']); ?>!</h4>
            <p>Anda berhasil login ke Aplikasi Kriptografi AES. Gunakan menu navigasi untuk mengakses fitur enkripsi, dekripsi, dan analisis algoritma.</p>
            <hr>
            <p class="mb-0" style="font-size:0.85em; font-style: italic;">Projek Skripsi Final: Pembuatan Aplikasi Berbasis Website Kriptografi Enkripsi dan Dekripsi Menggunakan Algoritma AES Beserta Analisis Perbandingan Penggunaan Antara AES 128 & 256 Bit.</p>
        </div>
    </div>

    <div class="product-sales-area mg-tb-30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><div class="stat-card text-center"><i class="fa fa-files-o stat-icon text-total-files" aria-hidden="true"></i><h4>Total Files</h4><p class="stat-number text-total-files"><?php echo $total_files; ?></p></div></div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><div class="stat-card text-center"><i class="fa fa-lock stat-icon text-encrypted" aria-hidden="true"></i><h4>Encrypted Files</h4><p class="stat-number text-encrypted"><?php echo $encrypted_files; ?></p></div></div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><div class="stat-card text-center"><i class="fa fa-unlock-alt stat-icon text-decrypted" aria-hidden="true"></i><h4>Decrypted Files</h4><p class="stat-number text-decrypted"><?php echo $decrypted_files; ?></p></div></div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><div class="stat-card text-center"><i class="fa fa-users stat-icon text-total-users" aria-hidden="true"></i><h4>Total Users</h4><p class="stat-number text-total-users"><?php echo $total_users; ?></p></div></div>
            </div>
            
            <div class="row" style="margin-top: 25px;">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="action-card card-encrypt">
                        <div class="action-icon-container">
                            <i class="fa fa-shield action-icon" aria-hidden="true"></i>
                        </div>
                        <div class="action-content">
                            <h3>Encrypt Your Files</h3>
                            <p>Amankan file penting Anda dengan enkripsi AES. Pilih antara kekuatan enkripsi AES-128 atau AES-256 untuk keamanan optimal.</p>
                            <a href="enkripsi.php" class="btn btn-action-card waves-effect waves-light"><i class="fa fa-arrow-right" aria-hidden="true"></i> Menuju Enkripsi</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="action-card card-decrypt">
                        <div class="action-icon-container">
                            <i class="fa fa-key action-icon" aria-hidden="true"></i>
                        </div>
                        <div class="action-content">
                            <h3>Decrypt Your Files</h3>
                            <p>Dekripsi file Anda yang telah terenkripsi AES dengan mudah. Pastikan Anda memiliki kunci yang benar untuk proses dekripsi.</p>
                            <a href="dekripsi.php" class="btn btn-action-card waves-effect waves-light"><i class="fa fa-arrow-right" aria-hidden="true"></i> Menuju Dekripsi</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 25px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="action-card card-analysis">
                         <div class="action-icon-container">
                            <i class="fa fa-bar-chart action-icon" aria-hidden="true"></i>
                        </div>
                        <div class="action-content">
                            <h3>AES Algorithm Analysis</h3>
                            <p>Jelajahi perbandingan kinerja antara algoritma AES-128 dan AES-256 melalui data analitik dan visualisasi grafik yang mendetail.</p>
                            <a href="dashboard-analisis-aes.php" class="btn btn-action-card waves-effect waves-light"><i class="fa fa-search-plus" aria-hidden="true"></i> Lihat Analisis</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-copyright-area">
        <div class="container-fluid"><div class="row"><div class="col-lg-12"><div class="footer-copy-right">
            <p>Copyright © <?php echo date("Y"); ?> Aplikasi Kriptografi AES by <?php echo htmlspecialchars($data_user['fullname']); ?>. All rights reserved.</p>
        </div></div></div></div>
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
<script>
    function adjustMainLayout() {
        var sidebarPro = $('.left-sidebar-pro');
        var sidebarWidth = 0;
        var rootStyles = getComputedStyle(document.documentElement);
        var defaultSidebarNormalWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-normal').trim()) || 250;
        var defaultSidebarMiniWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-mini').trim()) || 80;

        if (sidebarPro.length > 0 && sidebarPro.is(':visible')) {
            if ($('body').hasClass('mini-navbar')) { 
                sidebarWidth = defaultSidebarMiniWidth;
            } else {
                sidebarWidth = defaultSidebarNormalWidth;
            }
        }
        
        var headerTopArea = $('.header-top-area');
        var allContentWrapper = $('.all-content-wrapper');
        var headerHeight = headerTopArea.outerHeight() || parseFloat(rootStyles.getPropertyValue('--header-height').trim()) || 60;

        if (headerTopArea.css('position') === 'fixed') {
            headerTopArea.css({
                'left': sidebarWidth + 'px',
                'width': 'calc(100% - ' + sidebarWidth + 'px)'
            });
        }
        allContentWrapper.css({
            'margin-left': sidebarWidth + 'px',
            'padding-top': headerHeight + 'px'
        });
    }

    $(document).ready(function () {
        adjustMainLayout(); 

        var bodyNode = document.querySelector('body');
        if (bodyNode) {
            var observer = new MutationObserver(function(mutationsList, observer) {
                for(let mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        setTimeout(adjustMainLayout, 50); 
                        break; 
                    }
                }
            });
            observer.observe(bodyNode, { attributes: true });
        }
        $(window).on('resize', function() {
             setTimeout(adjustMainLayout, 50);
        });
    });
</script>
</body>
</html>