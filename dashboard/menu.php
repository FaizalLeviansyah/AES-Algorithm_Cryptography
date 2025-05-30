<?php if (!isset($_SESSION)) session_start(); ?>
<div class="left-sidebar-pro">
    <nav id="sidebar" class="">
        <div class="sidebar-header">
            <a href="index.php"><img class="main-logo" src="../img/logo/palw.png" alt="" /></a>
        </div>
        <div class="nalika-profile">
            <div class="profile-dtl">
                <h4 style="color:white;text-align:center"><?= $_SESSION['fullname'] ?></h4>
                <p style="text-align:center;color:lightgreen">(<?= $_SESSION['role'] ?>)</p>
            </div>
        </div>
        <div class="left-custom-menu-adp-wrap comment-scrollbar">
            <nav class="sidebar-nav left-sidebar-menu-pro">
                <ul class="metismenu" id="menu1">
                    <li><a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
                    <?php if ($_SESSION['role'] != 'reviewer'): ?>
                        <li><a href="enkripsi.php"><i class="fa fa-lock"></i> <span>Enkripsi</span></a></li>
                        <li><a href="dekripsi.php"><i class="fa fa-unlock"></i> <span>Dekripsi</span></a></li>
                    <?php endif; ?>
                    <li><a href="dashboard-analisis-aes.php"><i class="fa fa-bar-chart"></i> <span>Analisis AES</span></a></li>
                    <?php if ($_SESSION['role'] == 'superadmin'): ?>
                        <li><a href="user-management.php"><i class="fa fa-users"></i> <span>Kelola User</span></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </nav>
</div>