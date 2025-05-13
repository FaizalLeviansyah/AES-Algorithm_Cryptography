<?php
session_start();
include('../config.php');
if (empty($_SESSION['username'])) {
    header("location:../index.php");
}
$last = $_SESSION['username'];
mysqli_query($connect, "UPDATE users SET last_activity=now() WHERE username='$last'");
$user = $_SESSION['username'];
$data = mysqli_fetch_array(mysqli_query($connect, "SELECT fullname,job_title FROM users WHERE username='$user'"));
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Analisis AES</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/nalika-icon.css">
  <link rel="stylesheet" href="css/animate.css">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/meanmenu.min.css">
  <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
  <link rel="stylesheet" href="css/metisMenu/metisMenu.min.css">
  <link rel="stylesheet" href="css/metisMenu/metisMenu-vertical.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/custom-style-sidebar.css">
  <link rel="stylesheet" href="css/custom-style-sidebar-fixed.css">
  <link rel="stylesheet" href="css/light-mode.css" id="theme-style">
</head>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('toggle-theme');
    const themeLink = document.getElementById('theme-style');

    if (!toggleBtn || !themeLink) return;

    // Load state from localStorage
    const isLight = localStorage.getItem('theme') === 'light';
    setTheme(isLight);

    toggleBtn.addEventListener('click', function () {
      const isCurrentlyLight = themeLink.getAttribute('href').includes('light-mode.css');
      setTheme(!isCurrentlyLight);
    });

    function setTheme(useLight) {
      themeLink.setAttribute('href', useLight ? 'css/light-mode.css' : '');
      toggleBtn.textContent = useLight ? 'Dark Mode' : 'Light Mode';
      localStorage.setItem('theme', useLight ? 'light' : 'dark');
    }
  });
</script>

<body>
  <!-- Sidebar -->
  <div class="left-sidebar-pro">
    <nav id="sidebar" class="">
      <div class="sidebar-header">
        <a href="index.php"><img class="main-logo" src="img/logo/palw.png" alt="" /></a>
      </div>
      <div class="nalika-profile">
        <div class="profile-dtl">
          <a href="#"><img src="https://lh3.googleusercontent.com/ogw/ADGmqu-5A4r40ZPotQWqRs5qBqjF1pxruJuJs5TURuzdZw=s83-c-mo" alt="" /></a>
          <h2><?php echo $data['fullname']; ?><p class="designation icon" style="color:green;"><?php echo $data['job_title']; ?></p></h2>
        </div>
        <div class="profile-social-dtl">
          <ul class="dtl-social">
            <li><a href="#"><i class="icon nalika-facebook"></i></a></li>
            <li><a href="#"><i class="icon nalika-twitter"></i></a></li>
            <li><a href="#"><i class="icon nalika-linkedin"></i></a></li>
          </ul>
        </div>
      </div>
      <div class="left-custom-menu-adp-wrap comment-scrollbar">
        <nav class="sidebar-nav left-sidebar-menu-pro">
          <?php include('sidebar-nav-universal.php'); ?>
        </nav>
      </div>
    </nav>
  </div>

  <!-- Content Wrapper -->
  <div class="all-content-wrapper">
    <div class="container-fluid">
      <div class="row"><div class="col-lg-12"></div></div>
    </div>

    <div class="header-advance-area">
      <div class="header-top-area">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="header-top-wraper">
                <div class="row">
                  <div class="col-lg-1">
                    <div class="menu-switcher-pro">
                      <button id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
                        <i class="icon nalika-menu-task"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="breadcome-heading">
                      <form><input type="text" placeholder="Search..." class="form-control"></form>
                    </div>
                  </div>
                  <div class="col-lg-5">
                    <div class="header-right-info">
                      <button id="toggle-theme" class="btn btn-sm btn-outline-secondary" style="margin-right: 10px;">
  Light Mode
</button>

                      <ul class="nav navbar-nav mai-top-nav header-right-menu">
                        <li><a href="logout.php"> Log Out</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Analisis Table -->
      <section class="breadcome-list mt-5">
        <div class="card bg-dark text-light">
          <div class="card-body">
            <h4 class="text-info">📊 Analisis Perbandingan AES-128 dan AES-256</h4>
            <div class="table-responsive mt-3">
              <table class="table table-bordered text-light">
                <thead style="background-color:#e9ecef; color:#000;">
                  <tr>
                    <th>#</th>
                    <th>Nama File</th>
                    <th>Algoritma</th>
                    <th>Ukuran (KB)</th>
                    <th>Waktu Proses (ms)</th>
                    <th>Jenis Operasi</th>
                    <th>SHA-256 Hash</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $resultAES = mysqli_query($connect, "SELECT * FROM file WHERE alg_used IS NOT NULL ORDER BY tgl_upload DESC");
                    if (mysqli_num_rows($resultAES) === 0) {
                      echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data analisis AES.</td></tr>";
                    } else {
                      $no = 1;
                      while ($row = mysqli_fetch_assoc($resultAES)) {
                        echo "<tr>
                          <td class='text-light'>{$no}</td>
                          <td class='text-light'>" . htmlspecialchars($row['file_name_source']) . "</td>
                          <td class='text-light'>{$row['alg_used']}</td>
                          <td class='text-light'>" . round($row['file_size'], 2) . "</td>
                          <td class='text-light'>{$row['process_time_ms']}</td>
                          <td class='text-light'>" . ucfirst($row['operation_type']) . "</td>
                          <td class='text-light' style='font-size: 0.8rem;'>" . substr($row['hash_check'], 0, 12) . "...</td>
                          <td class='text-light'>" . date('Y-m-d H:i', strtotime($row['tgl_upload'])) . "</td>
                        </tr>";
                        $no++;
                      }
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

    </div>
  </div>

  <!-- Scripts -->
  <script src="js/vendor/jquery-1.12.4.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.meanmenu.js"></script>
  <script src="js/jquery.scrollUp.min.js"></script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
