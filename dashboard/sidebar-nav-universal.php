<?php
$current_page = basename($_SERVER['PHP_SELF']);
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
  <li class="<?= $current_page == 'dekripsi.php' ? 'active' : '' ?>">
    <a class="nav-link" href="dekripsi.php">
      <i class="icon nalika-unlocked icon-wrap"></i>
      <span class="mini-click-non">Dekripsi</span>
    </a>
  </li>
  <li class="<?= $current_page == 'dashboard-analisis-aes.php' ? 'active' : '' ?>">
    <a class="nav-link" href="dashboard-analisis-aes.php">
      <i class="icon nalika-bar-chart icon-wrap"></i>
      <span class="mini-click-non">Analisis AES</span>
    </a>
  </li>
</ul>
