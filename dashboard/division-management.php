<?php
// FILE: dashboard/division-management.php (CORRECTED)

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Authentication, Configuration, and Role Check ---
require_once __DIR__ . '/../auth_check.php';
if (!isset($connect)) {
    require_once __DIR__ . '/../config.php';
}
if ($_SESSION['role'] !== 'superadmin') {
    $_SESSION['global_message'] = "You do not have permission to access this page.";
    $_SESSION['global_message_type'] = "warning";
    header('Location: index.php');
    exit;
}

// --- Data Fetching ---
$divisions = [];
$result = mysqli_query($connect, "SELECT id, division_name FROM divisions ORDER BY division_name ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $divisions[] = $row;
    }
}

// --- Standard Template Variables ---
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_role_session = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_job_title_session = ucfirst($user_role_session);
$data_user = [ 'fullname' => $user_fullname_session, 'job_title' => $user_job_title_session ];
$base_css_path = 'css/';
$page_title = 'Manajemen Divisi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $page_title; ?> - Aplikasi Kriptografi AES</title>
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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>responsive.css">
    <link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/css/jquery.dataTables.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <style>
        /* --- ACCURATE CSS FROM DEKRIPSI.PHP --- */
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
            --sidebar-border-color: #E0E4E8 !important;
            --card-bg: #FFFFFF;
            --card-shadow: 0 2px 5px rgba(0,0,0,0.07);
            --card-border-radius: 8px;
            --text-color-default: #495057;
            --text-color-muted: #6c757d;
        }
        body { font-family: 'Roboto', sans-serif; font-size: 14px; background-color: var(--light-content-bg) !important; overflow-x: hidden; }
        .left-sidebar-pro { background-color: var(--sidebar-bg) !important; position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; width: var(--sidebar-width-normal) !important; z-index: 1032 !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; overflow: hidden; border-right: 1px solid var(--sidebar-border-color) !important; display: flex; flex-direction: column; }
        .header-top-area { background: var(--light-header-bg) !important; height: var(--header-height) !important; min-height: var(--header-height) !important; width: calc(100% - var(--sidebar-width-normal)) !important; display: flex !important; align-items: center !important; padding: 0 !important; box-sizing: border-box !important; position: fixed !important; top: 0 !important; left: var(--sidebar-width-normal) !important; z-index: 1030 !important; border-bottom: 1px solid var(--light-header-border) !important; box-shadow: var(--card-shadow) !important; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .all-content-wrapper { padding-top: var(--header-height) !important; margin-left: var(--sidebar-width-normal) !important; background: var(--light-content-bg) !important; min-height: calc(100vh - 56px); box-sizing: border-box; position: relative; transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow-x: hidden; padding-bottom: 70px; }
        body.mini-navbar .left-sidebar-pro { width: var(--sidebar-width-mini) !important; }
        body.mini-navbar .header-top-area { left: var(--sidebar-width-mini) !important; width: calc(100% - var(--sidebar-width-mini)) !important; }
        body.mini-navbar .all-content-wrapper { margin-left: var(--sidebar-width-mini) !important; }
        .header-top-wraper { width: 100% !important; height: 100% !important; padding: 0 20px !important; display: flex !important; align-items: center !important; justify-content: space-between !important; box-sizing: border-box !important; }
        .header-left-info { display: flex; align-items: center; flex-shrink: 0; }
        .menu-switcher-pro .navbar-btn { color: var(--light-text-secondary) !important; background-color: transparent !important; border: none !important; font-size: 1.5em !important; padding: 0 !important; margin-right: 15px !important; line-height: var(--header-height) !important; }
        .dashboard-title-header { color: var(--light-text-primary) !important; margin: 0 !important; font-size: 1.25em !important; font-weight: 500 !important; line-height: var(--header-height) !important; white-space: nowrap; }
        .header-right-info { display: flex; align-items: center; justify-content: flex-end; flex-grow: 1; overflow: visible; }
        .header-right-info .navbar-nav { display: flex; align-items: center; padding-left: 0; margin-bottom: 0; }
        .header-right-info .user-profile-area button { background-color: var(--sidebar-accent-color) !important; color: white !important; border: none !important; padding: 8px 15px !important; border-radius: 20px !important; font-size: 0.85em !important; font-weight: 500; transition: opacity 0.2s ease; }
        .author-log.dropdown-menu { right: 0px !important; left: auto !important; top: calc(100% + 10px) !important; box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; border: 1px solid var(--light-header-border) !important; border-radius: var(--card-border-radius) !important; margin-top: 0 !important; padding: 8px 0 !important; background-color: #fff !important; }
        .author-log.dropdown-menu > li > a { padding: 8px 18px !important; font-size: 0.9em !important; color: var(--light-text-primary) !important; display:flex; align-items:center; }
        .sidebar-header { padding: 0 !important; height: auto !important; min-height: calc(var(--header-height) + 70px) !important; background: linear-gradient(135deg, var(--sidebar-header-gradient-end), var(--sidebar-header-gradient-start)) !important; text-align: center !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; color: var(--sidebar-header-text-color) !important; }
        .sidebar-header .main-logo { max-width: 150px !important; max-height: 46px !important; height: auto !important; margin: 0 auto 10px auto !important; filter: brightness(0) invert(1); }
        .nalika-profile { padding: 0 15px 15px 15px !important; text-align: center !important; border-bottom: 1px solid var(--sidebar-border-color) !important; background: var(--sidebar-bg) !important; }
        .nalika-profile .profile-dtl h2 { color: var(--light-text-primary) !important; font-size: 0.95em !important; margin-bottom: 3px !important; font-weight: 500 !important; }
        .nalika-profile .profile-dtl .designation { font-size: 0.8em !important; color: var(--light-text-secondary) !important; display: block; }
        .left-custom-menu-adp-wrap { flex-grow: 1; overflow-y: auto; background-color: var(--sidebar-bg) !important; }
        .metismenu, .metismenu li { background-color: var(--sidebar-bg) !important; }
        .metismenu li a { color: var(--sidebar-text-color) !important; padding: 12px 20px !important; font-size: 0.9em !important; display: flex; align-items: center; transition: all 0.2s ease; border-left: 4px solid transparent !important; }
        .metismenu li a:hover, .metismenu li.active > a { background-color: var(--sidebar-hover-bg) !important; color: var(--sidebar-text-hover-color) !important; border-left-color: var(--sidebar-accent-color) !important; }
        .metismenu ul a { background-color: #fdfdfd !important; padding-left: 20px !important; }
        .content-wrap { padding: 20px 15px; }
        .breadcome-area-custom { background-color: transparent; padding: 15px 0px; border-bottom: 1px solid var(--light-header-border); margin-left: -15px; margin-right: -15px; padding-left: 15px; padding-right: 15px; margin-top: -20px; margin-bottom: 20px; }
        .breadcome-list-custom { padding: 0; margin: 0; list-style: none; display: flex; align-items: center; font-size: 0.9em; }
        .breadcome-list-custom li a { color: var(--sidebar-accent-color); text-decoration: none; }
        .breadcome-list-custom li .bread-slash { margin: 0 10px; color: var(--light-text-secondary); }
        .breadcome-list-custom li.active { color: var(--light-text-primary); font-weight: 500; }
        .table-container-card { background-color: var(--card-bg); padding: 25px 30px; border-radius: var(--card-border-radius); box-shadow: var(--card-shadow); }
        .table-container-card .table-title { color: var(--light-text-primary); margin-top: 0; margin-bottom: 10px; font-size: 1.5em; font-weight: 500; }
        .table-container-card .table-subtitle { color: var(--text-color-muted); font-size:0.9em; margin-bottom:25px; border-bottom: 1px solid var(--light-header-border); padding-bottom: 15px; }
        .table thead th { background-color: #f8f9fa; color: var(--light-text-primary); font-weight: 500; border-bottom-width: 2px; border-color: var(--light-header-border); font-size:0.8em; text-transform: uppercase; }
        .table tbody tr:hover { background-color: #f1f3f5; }
        .table td, .table th { vertical-align: middle; font-size: 0.9em; }
        .footer-copyright-area { background: var(--card-bg, #fff) !important; padding: 18px 0 !important; border-top: 1px solid var(--light-header-border) !important; position: fixed; bottom: 0; width: calc(100% - var(--sidebar-width-normal)); left: var(--sidebar-width-normal); z-index: 1000; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        body.mini-navbar .footer-copyright-area { left: var(--sidebar-width-mini) !important; width: calc(100% - var(--sidebar-width-mini)) !important; }
        .footer-copy-right p { color: var(--text-color-muted) !important; font-size: 0.85em; margin-bottom:0; text-align: center; }
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
                    <h2><?php echo htmlspecialchars($data_user['fullname']); ?></h2>
                    <span class="designation"><?php echo htmlspecialchars($data_user['job_title']); ?></span>
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
                    <h1 class="dashboard-title-header"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-right-info">
                    <ul class="nav navbar-nav mai-top-nav header-right-menu">
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
                                <li><a href="#">Administrasi</a> <span class="bread-slash">/</span></li>
                                <li class="active"><?php echo $page_title; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-container-card">
                            <h2 class="table-title"><i class="fa fa-sitemap"></i> <?php echo $page_title; ?></h2>
                            <p class="table-subtitle">Kelola semua divisi yang ada di dalam sistem.</p>
                             <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['message']; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                             <?php unset($_SESSION['message'], $_SESSION['message_type']); endif; ?>
                            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addDivisionModal"><i class="fa fa-plus"></i> Tambah Divisi Baru</button>
                            <div class="table-responsive">
                                <table class="table table-hover" id="divisionManagementTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Divisi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($divisions)): ?>
                                            <tr><td colspan="3" class="text-center">Belum ada data divisi.</td></tr>
                                        <?php else: ?>
                                            <?php $i = 1; foreach ($divisions as $division): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($division['division_name']); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="<?php echo $division['id']; ?>" data-name="<?php echo htmlspecialchars($division['division_name']); ?>" data-toggle="modal" data-target="#editDivisionModal"><i class="fa fa-pencil"></i> Edit</button>
                                                        <a href="division-process.php?action=delete&id=<?php echo $division['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus divisi ini?');"><i class="fa fa-trash"></i> Hapus</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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

    <div class="modal fade" id="addDivisionModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="division-process.php" method="post"><div class="modal-header"><h5 class="modal-title">Tambah Divisi</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><input type="hidden" name="action" value="add"><div class="form-group"><label>Nama Divisi</label><input type="text" class="form-control" name="division_name" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div></form></div></div></div>
    <div class="modal fade" id="editDivisionModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form action="division-process.php" method="post"><div class="modal-header"><h5 class="modal-title">Edit Divisi</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_division_id"><div class="form-group"><label>Nama Divisi</label><input type="text" class="form-control" id="edit_division_name" name="division_name" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Update</button></div></form></div></div></div>

    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.meanmenu.js"></script>
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
        $(document).ready(function() {
            $('#divisionManagementTable').DataTable({ "language": { "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" } });

            $('.edit-btn').on('click', function() {
                $('#edit_division_id').val($(this).data('id'));
                $('#edit_division_name').val($(this).data('name'));
            });

            // ACCURATE LAYOUT SCRIPT FROM DEKRIPSI.PHP
            function adjustMainLayout() {
                var sidebarWidth = $('body').hasClass('mini-navbar') ?
                    (parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width-mini')) || 80) :
                    (parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width-normal')) || 250);

                if ($(window).width() < 768) sidebarWidth = 0;

                $('.header-top-area, .footer-copyright-area').css({
                    'left': sidebarWidth + 'px',
                    'width': 'calc(100% - ' + sidebarWidth + 'px)'
                });
                $('.all-content-wrapper').css('margin-left', sidebarWidth + 'px');
            }

            var bodyNode = document.querySelector('body');
            if (bodyNode) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === "class") {
                             setTimeout(adjustMainLayout, 50);
                        }
                    });
                });
                observer.observe(bodyNode, { attributes: true });
            }
            $(window).on('resize', function() { setTimeout(adjustMainLayout, 50); });
            adjustMainLayout();
        });
    </script>
</body>
</html>