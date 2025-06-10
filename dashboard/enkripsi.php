<?php
session_start();
$is_reencrypt_mode = false;
$file_for_reencrypt = null;
if (isset($_GET['reencrypt_id'])) {
    // Logika untuk mengambil data file dari DB berdasarkan ID
    // ... (gunakan kode dari respons saya sebelumnya)
}
// Menggunakan auth_check.php untuk konsistensi dan keamanan sesi
require_once __DIR__ . '/../auth_check.php'; 
if (!isset($connect)) { 
    require_once __DIR__ . '/../config.php';
}

// Data pengguna dari sesi
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_job_title_session = ucfirst($user_role_session);

$data_user_display = [
    'fullname' => $user_fullname_session,
    'job_title' => $user_job_title_session
];

// Path gambar profil sidebar (konsisten dengan revisi sebelumnya)
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

// Pengecekan role
if ($user_role_session == 'reviewer') {
    $_SESSION['global_message'] = "Anda tidak memiliki izin untuk mengakses halaman enkripsi.";
    $_SESSION['global_message_type'] = "warning";
    header('Location: index.php'); 
    exit;
}

// Path CSS dasar
$base_css_path = 'css/'; 
$action_card_encrypt_bg = "linear-gradient(135deg, #2ecc71, #27ae60)";

// Logika untuk mode re-enkripsi
$is_reencrypt_mode = false;
$server_path_for_reencrypt_form = null; 
$display_filename_for_reencrypt_form = null;
$original_id_for_reencrypt_form = null;

if (isset($_GET['reencrypt_id']) && !empty($_GET['reencrypt_id'])) {
    $reencrypt_id_param = (int)$_GET['reencrypt_id'];
    $stmt_reencrypt = mysqli_prepare($connect, "SELECT id_file, file_name_source, file_url, status FROM file WHERE id_file = ? AND username = ? AND status = '2'");
    if ($stmt_reencrypt) {
        mysqli_stmt_bind_param($stmt_reencrypt, "is", $reencrypt_id_param, $_SESSION['username']); 
        mysqli_stmt_execute($stmt_reencrypt);
        $result_reencrypt = mysqli_stmt_get_result($stmt_reencrypt);
        $file_data_for_reencrypt = mysqli_fetch_assoc($result_reencrypt);
        mysqli_stmt_close($stmt_reencrypt);

        if ($file_data_for_reencrypt) {
            $potential_server_path = __DIR__ . '/../' . $file_data_for_reencrypt['file_url'];
            if (file_exists($potential_server_path) && is_readable($potential_server_path)) {
                $is_reencrypt_mode = true;
                $server_path_for_reencrypt_form = $file_data_for_reencrypt['file_url'];
                $display_filename_for_reencrypt_form = $file_data_for_reencrypt['file_name_source'];
                $original_id_for_reencrypt_form = $file_data_for_reencrypt['id_file'];
                if (!isset($_SESSION['encrypt_message'])) { 
                    $_SESSION['encrypt_message'] = "Mode enkripsi ulang untuk file: <strong>" . htmlspecialchars($display_filename_for_reencrypt_form) . "</strong>.";
                    $_SESSION['encrypt_message_type'] = "info";
                }
            } else {
                $_SESSION['encrypt_message'] = "File sumber untuk re-enkripsi tidak ditemukan atau tidak valid.";
                $_SESSION['encrypt_message_type'] = "error";
                $is_reencrypt_mode = false; 
            }
        } else {
            $_SESSION['encrypt_message'] = "Data file re-enkripsi tidak ditemukan atau Anda tidak memiliki izin.";
            $_SESSION['encrypt_message_type'] = "error";
        }
    } else {
         $_SESSION['encrypt_message'] = "Kesalahan database saat menyiapkan data re-enkripsi.";
         $_SESSION['encrypt_message_type'] = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Formulir Enkripsi File - Aplikasi Kriptografi AES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>nalika-icon.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>meanmenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu-vertical.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>scrollbar/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>animate.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>normalize.css">
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>responsive.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        /* --- CSS KUSTOM UTAMA (SAMA SEPERTI HALAMAN LAIN YANG SUDAH DIREVISI) --- */
        :root {
            --header-height: 60px; 
            --sidebar-width-normal: 250px; --sidebar-width-mini: 80px; --light-header-bg: #FFFFFF; --light-header-border: #E9EBF0; --light-content-bg: #F4F6F9; --light-text-primary: #343a40; --light-text-secondary: #6c757d; --light-icon-hover-bg: #f1f3f5; --sidebar-bg: #FFFFFF !important; --sidebar-header-gradient-start: #2ECC71 !important; --sidebar-header-gradient-end:rgb(39, 50, 174) !important; --sidebar-header-text-color: #FFFFFF !important; --sidebar-text-color: #4B5158 !important; --sidebar-text-hover-color:rgb(39, 50, 174) !important; --sidebar-hover-bg: #E9F7EF !important; --sidebar-active-bg: #D4EFDF !important; --sidebar-accent-color: rgb(39, 50, 174)  !important; --sidebar-accent-color-rgb: 39, 50, 174;  --sidebar-border-color: #E0E4E8 !important; --card-bg: #FFFFFF; --card-shadow: 0 2px 5px rgba(0,0,0,0.07); --card-hover-shadow: 0 4px 10px rgba(0,0,0,0.1); --card-border-radius: 8px; --text-color-default: #495057;  --text-color-muted: #6c757d; --action-card-encrypt-gradient: <?php echo $action_card_encrypt_bg; ?>;
        }
        body { font-family: 'Roboto', sans-serif; font-size: 14px; background-color: var(--light-content-bg) !important; overflow-x: hidden; color: var(--text-color-default); } 
        .left-sidebar-pro { background-color: var(--sidebar-bg) !important; position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; width: var(--sidebar-width-normal) !important; z-index: 1032 !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; overflow: hidden; border-right: 1px solid var(--sidebar-border-color) !important; display: flex; flex-direction: column; }
        body.mini-navbar .left-sidebar-pro { width: var(--sidebar-width-mini) !important; }
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
        .header-right-info .navbar-nav > li { margin-left: 5px; list-style: none; } 
        .header-right-info .navbar-nav > li:first-child { margin-left: 0; }
        .header-right-info .nav > li > a { color: var(--light-text-secondary) !important; padding: 7px !important; display: flex; align-items: center; border-radius: 50% !important; height: 34px !important; width: 34px !important; justify-content: center; transition: background-color 0.2s ease, color 0.2s ease; }
        .header-right-info .nav > li > a:hover, .header-right-info .nav > li > a:focus { color: var(--light-text-primary) !important; background-color: var(--light-icon-hover-bg) !important; }
        .header-right-info .nav > li > a > i { font-size: 1.1em !important; }
        .header-right-info .user-profile-area > a { padding: 5px 0px !important; height: auto !important; border-radius: 20px !important; width: auto !important; background-color: transparent !important; }
        .header-right-info .user-profile-area button { background-color: var(--sidebar-accent-color) !important; color: white !important; border: none !important; padding: 8px 15px !important; border-radius: 20px !important; font-size: 0.85em !important; font-weight: 500; transition: opacity 0.2s ease; }
        .header-right-info .user-profile-area button:hover { opacity: 0.85; }
        .author-log.dropdown-menu { right: 0px !important; left: auto !important; top: calc(100% + 10px) !important; box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; border: 1px solid var(--light-header-border) !important; border-radius: var(--card-border-radius) !important; margin-top: 0 !important; padding: 8px 0 !important; background-color: #fff !important; }
        .author-log.dropdown-menu > li > a { padding: 8px 18px !important; font-size: 0.9em !important; color: var(--light-text-primary) !important; display:flex; align-items:center; }
        .author-log.dropdown-menu > li > a:hover { background-color: var(--light-icon-hover-bg) !important; color: var(--sidebar-accent-color) !important; }
        .author-log.dropdown-menu > li > a .fa { color: var(--light-text-secondary) !important; margin-right: 10px; width:16px; text-align:center; }
        .author-log.dropdown-menu > li > a:hover .fa { color: var(--sidebar-accent-color) !important; }
        .sidebar-header { padding: 0 !important; height: auto !important; min-height: calc(var(--header-height) + 70px) !important; background: var(--sidebar-header-gradient-start); background: linear-gradient(135deg, var(--sidebar-header-gradient-end), var(--sidebar-header-gradient-start)) !important; text-align: center !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; border-bottom: none !important; color: var(--sidebar-header-text-color) !important; }
        .sidebar-header > a { display: block !important; line-height: normal !important; margin-top: 15px !important; padding: 0 !important; border: none !important; outline: none !important; box-shadow: none !important; text-decoration: none !important; }
        .sidebar-header .main-logo { max-width: 150px !important; max-height: 46px !important; height: auto !important; display: block !important; object-fit: contain !important; margin: 0 auto 10px auto !important; filter: brightness(0) invert(1); }
        .nalika-profile { padding: 0 15px 15px 15px !important; text-align: center !important; border-bottom: 1px solid var(--sidebar-border-color) !important; background: var(--sidebar-bg) !important; }
        .nalika-profile .profile-dtl { padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2); }
        .nalika-profile .profile-dtl img.profile-img-sidebar { width: 60px !important; height: 60px !important; border-radius: 50% !important; margin-bottom: 10px !important; border: 2px solid var(--sidebar-accent-color) !important; object-fit: cover; }
        .nalika-profile .profile-dtl h2 { color: var(--light-text-primary) !important; font-size: 0.95em !important; margin-bottom: 3px !important; font-weight: 500 !important; }
        .nalika-profile .profile-dtl .designation { font-size: 0.8em !important; color: var(--light-text-secondary) !important; display: block; }
        .left-custom-menu-adp-wrap { flex-grow: 1; overflow-y: auto; background-color: var(--sidebar-bg) !important; }
        .metismenu { background-color: var(--sidebar-bg) !important; padding-top:10px; padding-bottom: 20px; } .metismenu li { background-color: var(--sidebar-bg) !important; }
        .metismenu li a { color: var(--sidebar-text-color) !important; padding: 12px 20px !important; font-size: 0.9em !important; border-bottom: none !important; display: flex; align-items: center; transition: background-color 0.2s ease, color 0.2s ease, border-left-color 0.2s ease; }
        .metismenu li a:hover, .metismenu li.active > a { background-color: var(--sidebar-hover-bg) !important; color: var(--sidebar-text-hover-color) !important; border-left: 4px solid var(--sidebar-accent-color) !important; padding-left: 16px !important; }
        .metismenu li.active > a { background-color: var(--sidebar-active-bg) !important; font-weight: 500; }
        .metismenu li a .fa, .metismenu li a .nalika-icon { margin-right: 12px !important; font-size: 1.05em !important; width: 20px; text-align: center; flex-shrink: 0; color: var(--sidebar-text-color) !important; transition: color 0.2s ease; }
        .metismenu li a:hover .fa, .metismenu li a:hover .nalika-icon, .metismenu li.active > a .fa, .metismenu li.active > a .nalika-icon { color: var(--sidebar-text-hover-color) !important; }
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
        .form-container-card { background-color: var(--card-bg); padding: 30px 35px; border-radius: var(--card-border-radius); box-shadow: var(--card-shadow); margin-top: 0; }
        .form-container-card h2.form-title { color: var(--light-text-primary); margin-top: 0; margin-bottom: 10px; font-size: 1.5em; font-weight: 500; }
        .form-container-card p.form-subtitle { color: var(--text-color-muted); font-size:0.9em; margin-bottom:25px; border-bottom: 1px solid var(--light-header-border); padding-bottom: 15px; }
        .form-group label { font-weight: 500; color: var(--light-text-primary, #343a40); margin-bottom: .5rem; font-size: 0.9em; }
        .form-control, .custom-file-label { border-radius: 6px; border: 1px solid #ced4da; padding: .65rem 1rem; font-size: 0.92em; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; background-color: #fff; height: auto; color: var(--light-text-primary, #343a40); }
        .form-control::placeholder { color: var(--light-text-secondary, #6c757d); opacity: 1; }
        .form-control:focus { border-color: var(--sidebar-accent-color); box-shadow: 0 0 0 .2rem rgba(var(--sidebar-accent-color-rgb), 0.20); background-color: #fff; }
        .custom-file-input:focus ~ .custom-file-label { border-color: var(--sidebar-accent-color); box-shadow: 0 0 0 .2rem rgba(var(--sidebar-accent-color-rgb), 0.20); }
        .custom-file-label { color: var(--light-text-secondary); } .custom-file-label.selected { color: var(--light-text-primary); }
        .custom-file-label::after {  padding: .65rem 1rem; background-color: var(--light-text-secondary); color: white; border-left: 1px solid #ced4da; border-radius: 0 .375rem .375rem 0;  }
        .custom-file-input:lang(id) ~ .custom-file-label::after { content: "Telusuri"; }
        .input-group .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; }
        .input-group-append .btn { border-top-left-radius: 0; border-bottom-left-radius: 0; border: 1px solid #ced4da; border-left: none; background-color: #fff; color: var(--light-text-secondary); padding: .65rem .75rem; }
        .input-group:focus-within .form-control, .input-group:focus-within .input-group-append .btn { border-color: var(--sidebar-accent-color); }
        .input-group:focus-within .form-control { box-shadow: none; }
        .input-group:focus-within .input-group-append .btn { box-shadow: 0 0 0 .2rem rgba(var(--sidebar-accent-color-rgb), 0.20); z-index: 3; }
        textarea.form-control { min-height: 100px; }
        .btn-submit-custom { background: var(--action-card-encrypt-gradient) !important; border: none !important; color: var(--action-card-text-color) !important; padding: 12px 28px; font-size: 0.95em; font-weight: 500; border-radius: 25px; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-submit-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(var(--sidebar-accent-color-rgb),0.3); }
        .btn-submit-custom .fa { margin-right: 8px; }
        .alert-form-message { margin-top: 0px; margin-bottom: 20px; font-size: 0.9em; border-radius: var(--card-border-radius); padding: 12px 18px; }
        .alert-form-message .fa { margin-right: 8px; }
        .reencrypt-info-box { background-color: var(--sidebar-hover-bg); border-left: 4px solid var(--sidebar-accent-color); padding: 12px 18px; margin-bottom: 20px; border-radius: var(--card-border-radius); font-size: 0.9em; color: var(--text-color-default); }
        .reencrypt-info-box strong { color: var(--sidebar-accent-color); font-weight: 500; }
        @media (max-width: 991px) { .header-right-info .user-profile-details { display: none !important; } .header-right-info .nav > li.nav-item.d-none.d-md-flex {display: none!important;} .dashboard-title-header { font-size: 1.1em !important; } }
        @media (max-width: 767px) { .dashboard-title-header { font-size: 1em !important; white-space: normal; max-width: 120px; overflow: hidden; text-overflow: ellipsis; } .header-top-wraper { padding: 0 10px !important; } .header-right-info .navbar-nav > li { margin-left: 2px !important; } .header-right-info .nav > li > a.nav-link.dropdown-toggle { padding: 8px 5px !important; height: 34px !important; width: 34px !important; } .header-right-info .nav > li > a.nav-link.dropdown-toggle > i { font-size: 1.05em !important; } .menu-switcher-pro .navbar-btn { margin-right: 8px !important;} .footer-copyright-area { width: 100% !important; left: 0 !important; } .all-content-wrapper { margin-left: 0 !important; } body:not(.mini-navbar) .left-sidebar-pro { left: -250px !important; z-index: 1035; } body.mini-navbar .left-sidebar-pro { left: 0 !important; width: var(--sidebar-width-normal) !important; } body.mini-navbar .header-top-area, body.mini-navbar .all-content-wrapper, body.mini-navbar .footer-copyright-area { margin-left: 0 !important; left: 0 !important; width: 100% !important; } }
        @media (max-width: 480px) { .dashboard-title-header { font-size: 0.95em !important; max-width:100px; } .header-right-info .user-profile-area button { padding: 6px 10px !important; font-size: 0.8em !important; } }
    </style>
</head>
<body>

    <div class="left-sidebar-pro">
        <nav id="sidebar" class="">
            <div class="sidebar-header">
                <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo Aplikasi Utama" /></a>
                <strong class="d-none d-lg-block"><img src="img/logo/logosn.png" alt="Logo Kecil" /></strong>
            </div>
            <div class="nalika-profile">
                <div class="profile-dtl">
                    <h2><?php echo htmlspecialchars($data_user_display['fullname']); ?><p class="designation icon"><?php echo htmlspecialchars($data_user_display['job_title']); ?></p></h2>
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
                    <h1 class="dashboard-title-header">Formulir Enkripsi</h1>
                </div>
                <div class="header-right-info">
                    <ul class="nav navbar-nav mai-top-nav header-right-menu">
                        <li class="nav-item user-profile-area">
                            <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle" style="padding:0;">
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
                                <li class="active">Enkripsi File</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="form-container-card">
                            <h2 class="form-title">
                                <?php echo $is_reencrypt_mode ? 'Formulir Enkripsi Ulang File' : 'Formulir Enkripsi File Baru'; ?>
                            </h2>
                            <p class="form-subtitle">
                                <?php if ($is_reencrypt_mode): ?>
                                    Anda akan mengenkripsi ulang file: <strong><?php echo htmlspecialchars($display_filename_for_reencrypt_form); ?></strong>.
                                <?php else: ?>
                                    Unggah file baru, masukkan password, dan pilih algoritma AES untuk mengamankan file Anda.
                                <?php endif; ?>
                            </p>

                            <?php
                            if (isset($_SESSION['encrypt_message'])) {
                                $message_type_enkripsi = isset($_SESSION['encrypt_message_type']) ? $_SESSION['encrypt_message_type'] : 'info';
                                if (!in_array($message_type_enkripsi, ['success', 'danger', 'warning', 'info'])) $message_type_enkripsi = 'info';
                                $alert_icon_enkripsi = 'fa-info-circle';
                                if ($message_type_enkripsi == 'danger') $alert_icon_enkripsi = 'fa-times-circle';
                                if ($message_type_enkripsi == 'success') $alert_icon_enkripsi = 'fa-check-circle';
                                
                                echo '<div class="alert alert-' . $message_type_enkripsi . ' alert-dismissible fade show alert-form-message" role="alert">';
                                echo '<i class="fa ' . $alert_icon_enkripsi . '" aria-hidden="true"></i> ' . $_SESSION['encrypt_message'];
                                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                echo '</div>';
                                unset($_SESSION['encrypt_message'], $_SESSION['encrypt_message_type']);
                            }
                            ?>
                            
                            <form method="post" action="encrypt-process.php" enctype="multipart/form-data" id="encryptionForm">
                                <?php if ($is_reencrypt_mode && $server_path_for_reencrypt_form): ?>
                                    <input type="hidden" name="server_file_to_encrypt_path" value="<?php echo htmlspecialchars($server_path_for_reencrypt_form); ?>">
                                    <input type="hidden" name="original_filename_for_reencrypt" value="<?php echo htmlspecialchars($display_filename_for_reencrypt_form); ?>">
                                    <input type="hidden" name="original_id_for_reencrypt" value="<?php echo htmlspecialchars($original_id_for_reencrypt_form); ?>">
                                    
                                    <div class="form-group reencrypt-info-box">
                                        <label style="color: var(--light-text-primary); margin-bottom: 5px;">File yang akan Dienkripsi Ulang:</label>
                                        <p style="margin-bottom:0; font-size: 1.05em;"><strong><?php echo htmlspecialchars($display_filename_for_reencrypt_form); ?></strong></p>
                                    </div>
                                <?php else: ?>
                                    <div class="form-group">
                                        <label for="fileToEncrypt">Pilih File <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="file" id="fileToEncrypt" class="custom-file-input" required>
                                            <label class="custom-file-label" for="fileToEncrypt" data-browse="Telusuri">Pilih file...</label>
                                        </div>
                                        <small class="form-text text-muted">Maks: 5MB.</small>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="encryptionKey">Password / Kunci Enkripsi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="pwdfile" id="encryptionKey" class="form-control" placeholder="Masukkan password yang kuat" required autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordEncrypt" title="Tampilkan/Sembunyikan Password">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Akan digunakan untuk KDF (PBKDF2) & salt.</small>
                                </div>

                                <div class="form-group">
                                    <label for="aesAlgorithm">Pilih Algoritma AES & Mode <span class="text-danger">*</span></label>
                                    <select name="algorithm" id="aesAlgorithm" class="form-control" required>
                                        <option value="">-- Pilih Algoritma --</option>
                                        <option value="AES-128-CBC" selected>AES-128-CBC</option>
                                        <option value="AES-256-CBC">AES-256-CBC</option>
                                    </select>
                                    <small class="form-text text-muted">Mode CBC dengan IV unik akan digunakan.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="fileDescription">Deskripsi File (Opsional)</label>
                                    <textarea name="desc" id="fileDescription" class="form-control" rows="3" placeholder="Contoh: Dokumen penting proyek Alpha"></textarea>
                                </div>

                                <div class="text-center mt-4 pt-2">
                                    <button class="btn btn-submit-custom" name="encrypt_now_button" type="submit"> 
                                        <i class="fa fa-shield" aria-hidden="true"></i> 
                                        <?php echo $is_reencrypt_mode ? 'Proses Enkripsi Ulang' : 'Proses Enkripsi File'; ?>
                                    </button>
                                </div>
                            </form>
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
                             <p>Copyright © <?php echo date("Y"); ?> Aplikasi Kriptografi AES by <?php echo htmlspecialchars($data_user_display['fullname']); ?>. All rights reserved.</p>
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
    
    <script>
        $(document).ready(function () {
            // Skrip untuk custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || "Pilih file...");
            });

            // Skrip untuk toggle password
            const togglePasswordBtnEncrypt = document.querySelector('#togglePasswordEncrypt');
            const passwordInputElEncrypt = document.querySelector('#encryptionKey');
            if (togglePasswordBtnEncrypt && passwordInputElEncrypt) {
                togglePasswordBtnEncrypt.addEventListener('click', function () {
                    const type = passwordInputElEncrypt.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInputElEncrypt.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
            
            // Skrip untuk layout
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
            $('#sidebarCollapse').on('click', function () { 
                $('body').toggleClass('mini-navbar'); 
                if (typeof adjustMainLayout === "function") { 
                     setTimeout(adjustMainLayout, 50); 
                }
            });
        });
    </script>
</body>
</html>