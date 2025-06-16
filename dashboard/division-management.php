<?php
// FILE: dashboard/division-management.php (NEW FILE)
// This page allows the Admin to manage the divisions table (CRUD operations).

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Authentication and Configuration
require_once __DIR__ . '/../auth_check.php';
if (!isset($connect)) {
    require_once __DIR__ . '/../config.php';
}

// 2. Role Check: Only 'superadmin' can access this page
if ($_SESSION['role'] !== 'superadmin') {
    $_SESSION['global_message'] = "You do not have permission to access the Division Management page.";
    $_SESSION['global_message_type'] = "warning";
    header('Location: index.php');
    exit;
}

// 3. Fetch all existing divisions to display in the table
$divisions = [];
$result = mysqli_query($connect, "SELECT id, division_name FROM divisions ORDER BY division_name ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $divisions[] = $row;
    }
}

// --- Standard Template Setup (CSS, user data, etc.) ---
$user_fullname_session = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Pengguna';
$user_job_title_session = ucfirst(isset($_SESSION['job_title']) ? $_SESSION['job_title'] : $_SESSION['role']);
$data_user = ['fullname' => $user_fullname_session, 'job_title' => $user_job_title_session];
$base_css_path = 'css/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Divisi - Aplikasi Kriptografi AES</title>
    <!-- Include all your standard CSS files here (bootstrap, font-awesome, style.css, etc.) -->
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>nalika-icon.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>meanmenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>metisMenu/metisMenu-vertical.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="<?php echo $base_css_path; ?>responsive.css">
    <style>
        /* Include your standard custom CSS from other pages for a consistent look */
        /* For brevity, this is omitted, but you should copy it from dekripsi.php or another styled page */
        .table-container-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.07); }
        .page-title { margin-bottom: 20px; }
        .action-button-group .btn { margin-right: 5px; }
    </style>
</head>
<body class="">
    <div class="left-sidebar-pro">
        <nav id="sidebar" class="">
            <!-- Your Universal Sidebar Header & Profile -->
            <div class="sidebar-header">
                <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo"/></a>
                <strong><img src="img/logo/logosn.png" alt="Logo Mini"/></strong>
            </div>
            <div class="nalika-profile">
                <div class="profile-dtl">
                    <h2><?php echo htmlspecialchars($data_user['fullname']); ?></h2>
                    <p class="designation icon"><?php echo htmlspecialchars($data_user['job_title']); ?></p>
                </div>
            </div>
            <!-- Universal Sidebar Navigation Menu -->
            <div class="left-custom-menu-adp-wrap comment-scrollbar">
                <nav class="sidebar-nav left-sidebar-menu-pro">
                    <?php include('sidebar-nav-universal.php'); ?>
                </nav>
            </div>
        </nav>
    </div>

    <div class="all-content-wrapper">
        <!-- Universal Header -->
        <div class="header-top-area">
            <div class="header-top-wraper">
                <div class="header-left-info">
                    <div class="menu-switcher-pro">
                        <button type="button" id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
                            <i class="nalika-menu-task"></i>
                        </button>
                    </div>
                    <h1 class="dashboard-title-header">Manajemen Divisi</h1>
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

        <!-- Main Content -->
        <div class="content-wrap">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-container-card">
                            <h2 class="page-title">Daftar Divisi</h2>
                            <p>Kelola semua divisi yang ada di dalam sistem.</p>
                            
                            <!-- Display session messages -->
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION['message']; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                            <?php endif; ?>

                            <!-- Add New Division Button -->
                            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addDivisionModal">
                                <i class="fa fa-plus"></i> Tambah Divisi Baru
                            </button>

                            <!-- Divisions Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Divisi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($divisions)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Belum ada data divisi.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $i = 1; foreach ($divisions as $division): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($division['division_name']); ?></td>
                                                    <td class="action-button-group">
                                                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="<?php echo $division['id']; ?>" data-name="<?php echo htmlspecialchars($division['division_name']); ?>" data-toggle="modal" data-target="#editDivisionModal">
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </button>
                                                        <a href="division-process.php?action=delete&id=<?php echo $division['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus divisi ini?');">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </a>
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
    </div>

    <!-- Add Division Modal -->
    <div class="modal fade" id="addDivisionModal" tabindex="-1" role="dialog" aria-labelledby="addDivisionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="division-process.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDivisionModalLabel">Tambah Divisi Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label for="division_name">Nama Divisi</label>
                            <input type="text" class="form-control" id="division_name" name="division_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Division Modal -->
    <div class="modal fade" id="editDivisionModal" tabindex="-1" role="dialog" aria-labelledby="editDivisionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="division-process.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDivisionModalLabel">Edit Divisi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_division_id">
                        <div class="form-group">
                            <label for="edit_division_name">Nama Divisi</label>
                            <input type="text" class="form-control" id="edit_division_name" name="division_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include all your standard JS files -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/metisMenu/metisMenu.min.js"></script>
    <script src="js/metisMenu/metisMenu-active.js"></script>
    <script src="js/main.js"></script>

    <script>
        // Script to populate the edit modal with data
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                $('#edit_division_id').val(id);
                $('#edit_division_name').val(name);
            });
        });
    </script>
</body>
</html>