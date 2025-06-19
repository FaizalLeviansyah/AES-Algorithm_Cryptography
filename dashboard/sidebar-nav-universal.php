<?php
// FILE: dashboard/sidebar-nav-universal.php (REPLACE EXISTING FILE)
// This version has corrected icons and hover states.

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>
<ul class="metismenu" id="menu1">
  <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
    <a class="nav-link" href="index.php">
      <i class="icon nalika-home icon-wrap"></i>
      <span class="mini-click-non">Dashboard</span>
    </a>
  </li>
  <li class="<?= $current_page == 'enkripsi.php' ? 'active' : '' ?>">
    <a class="nav-link" href="enkripsi.php">
      <i class="icon nalika-unlocked icon-wrap"></i>
      <span class="mini-click-non">Enkripsi</span>
    </a>
  </li>
  <li class="<?= ($current_page == 'dekripsi.php' || $current_page == 'decrypt-file.php') ? 'active' : '' ?>">
    <a class="nav-link" href="dekripsi.php">
      <i class="fa fa-key icon-wrap"></i>
      <span class="mini-click-non">Dekripsi</span>
    </a>
  </li>
  <li class="<?= $current_page == 'dashboard-analisis-aes.php' ? 'active' : '' ?>">
    <a class="nav-link" href="dashboard-analisis-aes.php">
      <i class="icon nalika-bar-chart icon-wrap"></i>
      <span class="mini-click-non">Analisis AES</span>
    </a>
  </li>
  
  <?php
  // This section will only be visible if the logged-in user's role is 'superadmin'.
  if ($user_role === 'superadmin'):
    $is_admin_page = in_array($current_page, ['user-management.php', 'division-management.php']);
  ?>
    <li class="<?= $is_admin_page ? 'active' : '' ?>">
      <a class="has-arrow" href="#" aria-expanded="<?= $is_admin_page ? 'true' : 'false' ?>">
        <i class="icon nalika-settings icon-wrap"></i>
        <span class="mini-click-non">Administrasi</span>
      </a>
      <ul class="submenu-angle" aria-expanded="<?= $is_admin_page ? 'true' : 'false' ?>">
        <li class="<?= $current_page == 'user-management.php' ? 'active' : '' ?>">
          <a title="User Management" href="user-management.php"><span class="mini-sub-pro">Manajemen Pengguna</span></a>
        </li>
        <li class="<?= $current_page == 'division-management.php' ? 'active' : '' ?>">
          <a title="Division Management" href="division-management.php"><span class="mini-sub-pro">Manajemen Divisi</span></a>
        </li>
      </ul>
    </li>
  <?php endif; ?>
</ul>