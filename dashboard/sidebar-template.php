<?php
// FILE: dashboard/sidebar-template.php (NEW HELPER FILE)
// This file contains the universal sidebar HTML structure to avoid repetition.

// This assumes that the following variables are defined in the page that includes this file:
// $data_user (array with 'fullname' and 'job_title')
?>
<nav id="sidebar" class="">
    <div class="sidebar-header">
        <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="Logo Aplikasi Utama" /></a>
        <strong><img src="img/logo/logosn.png" alt="Logo Kecil" /></strong>
    </div>
    <div class="nalika-profile">
        <div class="profile-dtl">
            <!-- You can add a profile picture here if you have one in the session -->
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