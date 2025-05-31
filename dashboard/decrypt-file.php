<?php
// File: dashboard/decrypt-file.php (REVISI UI)

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../auth_check.php'; // auth_check.php akan memulai session
if (!isset($connect)) {
    require_once __DIR__ . '/../config.php';
}

// Data pengguna dari sesi (diasumsikan auth_check.php sudah mengisi ini)
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // Ambil role dari sesi
$user_job_title_session = ucfirst($user_role_session);

// Data user untuk sidebar (mengikuti pola dashboard/index.php dan dekripsi.php)
if (!isset($data_user)) {
    $data_user = [
        'fullname' => $user_fullname_session,
        'job_title' => $user_job_title_session
    ];
}
// Path untuk gambar profil di sidebar (disamakan dengan dekripsi.php)
$user_profile_pic_path_sidebar = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'img/contact/default-user.png';
$user_profile_pic_sidebar = file_exists(__DIR__ . '/../' . $user_profile_pic_path_sidebar) ? '../' . $user_profile_pic_path_sidebar : (file_exists(__DIR__ . '/' . $user_profile_pic_path_sidebar) ? $user_profile_pic_path_sidebar : 'img/contact/default-user.png');
if (!file_exists($user_profile_pic_sidebar) && strpos($user_profile_pic_sidebar, 'default-user.png') !== false) {
    $user_profile_pic_sidebar = '../img/contact/default-user.png';
     if (!file_exists($user_profile_pic_sidebar)) {
        $user_profile_pic_sidebar = 'img/contact/default-user.png';
     }
}


$id_file_to_process = null;
$file_info = null;
$error_message_form = '';

if (isset($_GET['id_file'])) {
    $id_file_to_process = mysqli_real_escape_string($connect, $_GET['id_file']);
    $query_file_info_sql = "SELECT id_file, file_name_source, file_name_finish, file_url, alg_used, status, username, keterangan FROM file WHERE id_file = ?";
    $stmt_file_info = mysqli_prepare($connect, $query_file_info_sql);
    if ($stmt_file_info) {
        mysqli_stmt_bind_param($stmt_file_info, "s", $id_file_to_process);
        mysqli_stmt_execute($stmt_file_info);
        $result_file_info = mysqli_stmt_get_result($stmt_file_info);
        $file_info = mysqli_fetch_array($result_file_info, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_file_info);

        if (!$file_info) {
            $_SESSION['dekripsi_message'] = "Error: File tidak ditemukan (ID: " . htmlspecialchars($id_file_to_process) . ").";
            $_SESSION['dekripsi_message_type'] = "error";
            header('Location: dekripsi.php');
            exit;
        }
        // Gunakan $user_role_session untuk pemeriksaan izin
        if (!($user_role_session == 'superadmin' || $user_role_session == 'admin') && $file_info['username'] !== $_SESSION['username']) {
            $_SESSION['dekripsi_message'] = "Error: Anda tidak memiliki izin untuk file ini.";
            $_SESSION['dekripsi_message_type'] = "error";
            header('Location: dekripsi.php');
            exit;
        }
        if ($file_info['status'] == 2) { // Sudah terdekripsi (disalin)
            $_SESSION['dekripsi_message'] = "Info: File '" . htmlspecialchars($file_info['file_name_source']) . "' sudah 'didekripsi' (disalin).";
            $_SESSION['dekripsi_message_type'] = "info";
            header('Location: dekripsi.php');
            exit;
        }
        if ($file_info['status'] != 1) { // Bukan terenkripsi
            $_SESSION['dekripsi_message'] = "Info: File '" . htmlspecialchars($file_info['file_name_source']) . "' tidak dalam status untuk didekripsi.";
            $_SESSION['dekripsi_message_type'] = "warning";
            header('Location: dekripsi.php');
            exit;
        }
    } else {
        $error_message_form = "Gagal menyiapkan query informasi file: " . mysqli_error($connect);
    }
} else {
    $_SESSION['dekripsi_message'] = "Error: ID file tidak disediakan.";
    $_SESSION['dekripsi_message_type'] = "error";
    header('Location: dekripsi.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decrypt_now_button'])) {
    $user_password_input_decrypt = $_POST['pwdfile_decrypt'];

    if (empty($user_password_input_decrypt)) {
        $error_message_form = "Password tidak boleh kosong (meskipun hanya simulasi).";
    } elseif ($file_info) {
        $source_file_path_on_server = '../' . $file_info['file_url'];

        if (!file_exists($source_file_path_on_server)) {
            $error_message_form = "File sumber tidak ditemukan di server: " . htmlspecialchars($source_file_path_on_server);
        } else {
            $start_time_decrypt_sim = microtime(true);
            $original_filename_no_ext = pathinfo($file_info['file_name_source'], PATHINFO_FILENAME);
            $original_ext = pathinfo($file_info['file_name_source'], PATHINFO_EXTENSION);
            $decrypted_file_name_on_server = 'dec_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $original_filename_no_ext) . (empty($original_ext) ? '' : '.' . $original_ext);
            $decrypted_folder_server = __DIR__ . '/../decrypted_result/';
            if (!is_dir($decrypted_folder_server)) {
                if(!mkdir($decrypted_folder_server, 0755, true)) {
                    $error_message_form = "Error Kritis: Gagal membuat direktori decrypted_result.";
                }
            }
            
            if(empty($error_message_form)) {
                $decrypted_file_path_on_server = $decrypted_folder_server . $decrypted_file_name_on_server;
                $file_url_for_db_decrypted = 'decrypted_result/' . $decrypted_file_name_on_server;

                if (!copy($source_file_path_on_server, $decrypted_file_path_on_server)) {
                    $error_message_form = "Gagal menyalin file ke folder decrypted_result.";
                } else {
                    $db_process_time_ms_decrypt = round((microtime(true) - $start_time_decrypt_sim) * 1000, 2);
                    $db_hash_after_decryption = hash_file('sha256', $decrypted_file_path_on_server);
                    $db_status_decrypted = 2;
                    $db_operation_type_decrypted = 'dekripsi (simulasi)';

                    $update_sql = "UPDATE file SET 
                                    status = ?, 
                                    file_name_finish = ?, 
                                    file_url = ?, 
                                    process_time_ms = ?, 
                                    operation_type = ?, 
                                    hash_check = ? 
                                   WHERE id_file = ?";
                    $stmt_update = mysqli_prepare($connect, $update_sql);
                    if ($stmt_update) {
                        mysqli_stmt_bind_param($stmt_update, "issdsss", 
                            $db_status_decrypted, 
                            $decrypted_file_name_on_server, 
                            $file_url_for_db_decrypted, 
                            $db_process_time_ms_decrypt, 
                            $db_operation_type_decrypted, 
                            $db_hash_after_decryption, 
                            $id_file_to_process
                        );
                        if (mysqli_stmt_execute($stmt_update)) {
                            $_SESSION['dekripsi_message'] = "File '" . htmlspecialchars($file_info['file_name_source']) . "' berhasil 'didekripsi' (disalin).";
                            $_SESSION['dekripsi_message_type'] = "success";
                            header('Location: dekripsi.php');
                            exit;
                        } else {
                            $error_message_form = "Gagal update DB setelah 'dekripsi': " . mysqli_stmt_error($stmt_update);
                        }
                        mysqli_stmt_close($stmt_update);
                    } else {
                         $error_message_form = "Gagal menyiapkan statement update DB: " . mysqli_error($connect);
                    }
                }
            }
        }
    }
}

// Path CSS (relatif dari dashboard/)
$base_css_path = 'css/';
$action_card_decrypt_bg_form = "linear-gradient(135deg, #3498db, #2980b9)"; // Sama dengan di dekripsi.php
$custom_sidebar_css_path = $base_css_path . 'custom-style-sidebar.css';
$custom_sidebar_fixed_css_path = $base_css_path . 'custom-style-sidebar-fixed.css';
$include_custom_sidebar_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_css_path);
$include_custom_sidebar_fixed_css = file_exists(dirname(__FILE__) . '/' . $custom_sidebar_fixed_css_path);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Dekripsi File - Aplikasi Kriptografi AES</title>
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
    <link rel="stylesheet" href="style.css"> <?php if ($include_custom_sidebar_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_css_path; ?>">
    <?php endif; ?>
    <?php if ($include_custom_sidebar_fixed_css): ?>
        <link rel="stylesheet" href="<?php echo $custom_sidebar_fixed_css_path; ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo $base_css_path; ?>responsive.css">
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

            --sidebar-bg: #FFFFFF !important;
            --sidebar-header-gradient-start: #2ECC71 !important;
            --sidebar-header-gradient-end:rgb(39, 50, 174) !important;
            --sidebar-header-text-color: #FFFFFF !important;
            --sidebar-text-color: #4B5158 !important;
            --sidebar-text-hover-color:rgb(39, 50, 174) !important;
            --sidebar-hover-bg: #E9F7EF !important;
            --sidebar-active-bg: #D4EFDF !important;
            --sidebar-accent-color: rgb(39, 50, 174)  !important;
            --sidebar-accent-color-rgb: 39, 50, 174; /* Untuk box-shadow & form focus */
            --sidebar-border-color: #E0E4E8 !important;

            --card-bg: #FFFFFF;
            --card-shadow: 0 2px 5px rgba(0,0,0,0.07);
            --card-hover-shadow: 0 4px 10px rgba(0,0,0,0.1);
            --card-border-radius: 8px;

            --text-color-default: #495057;
            --text-color-muted: #6c757d;

            --action-card-encrypt-gradient: linear-gradient(135deg, #2ecc71, #27ae60);
            --action-card-decrypt-gradient: <?php echo $action_card_decrypt_bg_form; ?>;
            --action-card-analysis-gradient: linear-gradient(135deg, #9b59b6, #8e44ad);
            --action-card-text-color: #FFFFFF;
        }
        /* ... (SEMUA CSS LAINNYA DARI BLOK STYLE dekripsi.php YANG SUDAH DIREVISI, KECUALI CSS SPESIFIK TABEL) ... */
        body { font-family: 'Roboto', sans-serif; font-size: 14px; background-color: var(--light-content-bg) !important; overflow-x: hidden; }
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

        /* --- MULAI CSS SPESIFIK UNTUK decrypt-file.php --- */
        .form-container-card {
            background-color: var(--card-bg);
            padding: 30px 35px; /* Lebih banyak padding untuk form */
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            margin-top: 0; /* Dihapus karena .content-wrap memberi jarak */
        }
        .form-container-card h2.form-title {
            color: var(--light-text-primary);
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.5em;
            font-weight: 500;
        }
        .form-container-card p.form-subtitle {
            color: var(--text-color-muted);
            font-size:0.9em;
            margin-bottom:25px;
            border-bottom: 1px solid var(--light-header-border);
            padding-bottom: 15px;
        }
        .form-group label {
            font-weight: 500;
            color: var(--light-text-secondary); /* Lebih gelap dari var(--text-color-muted) */
            margin-bottom: .5rem;
            font-size: 0.9em;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da; /* Warna border standar Bootstrap */
            padding: .65rem 1rem; /* Padding lebih nyaman */
            font-size: 0.92em;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            background-color: #fff; /* Pastikan background putih */
            height: auto; /* Agar padding efektif */
        }
        .form-control:focus {
            border-color: var(--sidebar-accent-color);
            box-shadow: 0 0 0 .2rem rgba(var(--sidebar-accent-color-rgb), 0.20);
            background-color: #fff; /* Pastikan background putih saat fokus */
        }
        /* Styling untuk input group password toggle */
        .input-group .form-control { /* Agar border-radius kanan tidak terpotong */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none; /* Tombol akan menyediakan border kanan */
        }
        .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border: 1px solid #ced4da;
            border-left: none;
            background-color: #fff; /* Latar tombol show/hide password */
            color: var(--light-text-secondary);
            padding: .65rem .75rem;
        }
        .input-group-append .btn:hover {
            background-color: #f8f9fa;
        }
        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-append .btn {
            border-color: var(--sidebar-accent-color); /* Border group saat input fokus */
        }
        .input-group:focus-within .form-control {
             box-shadow: none; /* Hanya group yang dapat shadow */
        }
        .input-group:focus-within .input-group-append .btn {
            box-shadow: 0 0 0 .2rem rgba(var(--sidebar-accent-color-rgb), 0.20);
            z-index: 3; /* Agar shadow tombol terlihat di atas form-control */
        }

        .btn-submit-custom { /* Tombol utama form */
            background: var(--action-card-decrypt-gradient) !important; /* Menggunakan variabel global */
            border: none !important;
            color: var(--action-card-text-color) !important; /* Menggunakan variabel global */
            padding: 12px 28px;
            font-size: 0.95em;
            font-weight: 500;
            border-radius: 25px; /* Rounded button */
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-submit-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(var(--sidebar-accent-color-rgb),0.3); /* Shadow lebih menonjol saat hover */
        }
        .btn-submit-custom .fa { margin-right: 8px; }

        .btn-cancel-custom { /* Tombol batal */
            padding: 11px 25px;
            font-size: 0.95em;
            font-weight: 500;
            border-radius: 25px;
            border: 1px solid var(--light-text-secondary);
            color: var(--light-text-secondary);
            background-color: transparent;
            transition: all 0.3s ease;
        }
        .btn-cancel-custom:hover {
            background-color: var(--light-text-secondary);
            color: #fff;
            border-color: var(--light-text-secondary);
        }


        .alert-form-message { /* Pesan error/sukses di dalam form */
            margin-top: 0px; /* Jika di atas form */
            margin-bottom: 20px;
            font-size: 0.9em;
            border-radius: var(--card-border-radius);
            padding: 12px 18px;
        }
         .alert-form-message .fa { margin-right: 8px; }

        .file-info-box {
            background-color: #e9f7ef; /* Warna hijau muda, atau var(--sidebar-hover-bg) */
            padding: 15px 20px;
            border-radius: var(--card-border-radius);
            margin-bottom: 25px;
            font-size: 0.9em;
            border-left: 4px solid var(--sidebar-accent-color);
        }
        .file-info-box strong {
            color: var(--light-text-primary);
            font-weight: 500;
        }
        /* --- AKHIR CSS SPESIFIK --- */
    </style>
</head>
<body class="">

    <div class="left-sidebar-pro">
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
                    <h1 class="dashboard-title-header">Proses Dekripsi File</h1>
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
                                <li><a href="dekripsi.php">Daftar File</a> <span class="bread-slash">/</span></li>
                                <li class="active">Proses Dekripsi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto"> {/* Form di tengah dan tidak terlalu lebar */}
                        <div class="form-container-card">
                            <h2 class="form-title">Formulir Dekripsi File</h2>
                            <?php if ($file_info): ?>
                                <p class="form-subtitle">
                                    Anda akan memproses file: <strong><?php echo htmlspecialchars($file_info['file_name_source']); ?></strong>.
                                </p>
                                <div class="file-info-box">
                                    <strong>Nama File "Terenkripsi":</strong> <?php echo htmlspecialchars($file_info['file_name_finish']); ?><br>
                                    <strong>Algoritma Tercatat:</strong> <span class="badge badge-info" style="font-size: 0.9em;"><?php echo htmlspecialchars($file_info['alg_used']); ?></span>
                                </div>
                            <?php else: ?>
                                <p class="form-subtitle text-danger">Informasi file tidak dapat dimuat.</p>
                            <?php endif; ?>

                            <?php if (!empty($error_message_form)): ?>
                                <div class="alert alert-danger alert-dismissible fade show alert-form-message" role="alert">
                                    <i class="fa fa-times-circle" aria-hidden="true"></i> <?php echo $error_message_form; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            <?php endif; ?>

                            <?php if ($file_info && $file_info['status'] == 1): ?>
                            <form method="post" action="decrypt-file.php?id_file=<?php echo htmlspecialchars($id_file_to_process); ?>" id="decryptionForm">
                                <div class="form-group">
                                    <label for="decryptionKey">Password / Kunci <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="pwdfile_decrypt" id="decryptionKey" class="form-control" placeholder="Masukkan password (formalitas)" required autocomplete="current-password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordDecrypt" title="Tampilkan/Sembunyikan Password">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Karena enkripsi hanya simulasi, password ini hanya formalitas.</small>
                                </div>
                                <div class="text-center mt-4 pt-2"> {/* Tambah padding atas untuk jarak tombol */}
                                    <button class="btn btn-submit-custom" name="decrypt_now_button" type="submit">
                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i> "Dekripsi" File (Salin)
                                    </button>
                                    <a href="dekripsi.php" class="btn btn-cancel-custom ml-2">Batal</a>
                                </div>
                            </form>
                            <?php elseif($file_info && $file_info['status'] != 1 && empty($error_message_form)): ?>
                                <div class="alert alert-info alert-form-message text-center">
                                    <i class="fa fa-info-circle"></i> File ini tidak dalam status untuk "didekripsi".
                                </div>
                                <div class="text-center mt-3">
                                     <a href="dekripsi.php" class="btn btn-primary" style="border-radius:25px; padding: 10px 20px;">Kembali ke Daftar File</a>
                                </div>
                            <?php elseif(!$file_info && empty($error_message_form)): // Kasus jika $file_info gagal dimuat dan tidak ada error spesifik dari POST
                                echo '<div class="alert alert-warning alert-form-message text-center"><i class="fa fa-exclamation-triangle"></i> Tidak dapat memuat detail file untuk dekripsi. Silakan kembali dan coba lagi.</div>';
                                echo '<div class="text-center mt-3"><a href="dekripsi.php" class="btn btn-primary" style="border-radius:25px; padding: 10px 20px;">Kembali ke Daftar File</a></div>';
                            endif; ?>
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
    <script>
        $(document).ready(function () {
            const togglePasswordBtnDecrypt = document.querySelector('#togglePasswordDecrypt');
            const passwordInputElDecrypt = document.querySelector('#decryptionKey');
            if (togglePasswordBtnDecrypt && passwordInputElDecrypt) {
                togglePasswordBtnDecrypt.addEventListener('click', function () {
                    const type = passwordInputElDecrypt.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInputElDecrypt.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }

            // SALIN FUNGSI adjustMainLayout DARI DASHBOARD/INDEX.PHP (atau dekripsi.php yang sudah direvisi)
            function adjustMainLayout() {
                var sidebarPro = $('.left-sidebar-pro');
                var sidebarWidth = 0;
                var rootStyles = getComputedStyle(document.documentElement);
                var defaultSidebarNormalWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-normal').trim()) || 250;
                var defaultSidebarMiniWidth = parseFloat(rootStyles.getPropertyValue('--sidebar-width-mini').trim()) || 80;
                var headerHeight = parseFloat(rootStyles.getPropertyValue('--header-height').trim()) || 60;
                var footerArea = $('.footer-copyright-area');
                var footerHeight = (footerArea.length > 0 && footerArea.css('position') === 'fixed') ? (footerArea.outerHeight() || 56) : 0;

                if ($(window).width() >= 768) {
                    if (sidebarPro.length > 0 && sidebarPro.is(':visible')) {
                        if ($('body').hasClass('mini-navbar')) { sidebarWidth = defaultSidebarMiniWidth; } else { sidebarWidth = defaultSidebarNormalWidth; }
                    }
                } else { sidebarWidth = 0; }

                var headerTopArea = $('.header-top-area');
                var allContentWrapper = $('.all-content-wrapper');

                if (headerTopArea.css('position') === 'fixed') {
                     if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) { headerTopArea.css({ 'left': sidebarWidth + 'px', 'width': 'calc(100% - ' + sidebarWidth + 'px)' }); } 
                     else { headerTopArea.css({ 'left': '0px', 'width': '100%'}); }
                }
                if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) {
                    allContentWrapper.css({ 'margin-left': sidebarWidth + 'px', 'padding-top': headerHeight + 'px', 'padding-bottom': (footerHeight + 20) + 'px' });
                } else { allContentWrapper.css({ 'margin-left': '0px', 'padding-top': headerHeight + 'px', 'padding-bottom': (footerHeight + 20) + 'px' }); }

                if (footerArea.length > 0 && footerArea.css('position') === 'fixed') {
                    if ($(window).width() >= 768 || !$('body').hasClass('mini-navbar')) { footerArea.css({ 'left': sidebarWidth + 'px', 'width': 'calc(100% - ' + sidebarWidth + 'px)' }); } 
                    else { footerArea.css({ 'left': '0px', 'width': '100%'}); }
                }
            }

            adjustMainLayout();
            var bodyNode = document.querySelector('body');
            if (bodyNode) {
                var observer = new MutationObserver(function(mutationsList) {
                    for(let mutation of mutationsList) { if (mutation.type === 'attributes' && mutation.attributeName === 'class') { setTimeout(adjustMainLayout, 50); if ($(window).width() < 768) { if ($('body').hasClass('mini-navbar')) { $('#sidebarCollapse').addClass('active'); } else { $('#sidebarCollapse').removeClass('active'); } } break; } }
                });
                observer.observe(bodyNode, { attributes: true });
            }
            $(window).on('resize', function() { setTimeout(adjustMainLayout, 50); });
            $('#sidebarCollapse').on('click', function () { if ($(window).width() < 768) { $('body').toggleClass('mini-navbar'); } });
        });
    </script>
</body>
</html>