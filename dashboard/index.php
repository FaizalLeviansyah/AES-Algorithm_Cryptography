<?php
session_start();
include('../config.php');
if(empty($_SESSION['username'])){
header("location:../index.php");
}
$last = $_SESSION['username'];
$sqlupdate = "UPDATE users SET last_activity=now() WHERE username='$last'";
$queryupdate = mysqli_query($connect,$sqlupdate);
?>
<!DOCTYPE html>
<html>
<?php
$user = $_SESSION['username'];
$query = mysqli_query($connect,"SELECT fullname,job_title,last_activity FROM users WHERE username='$user'");
$data = mysqli_fetch_array($query);
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>File Enkripsi & Dekripsi AES</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- nalika Icon CSS
		============================================ -->
    <link rel="stylesheet" href="css/nalika-icon.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- meanmenu icon CSS
		============================================ -->
    <link rel="stylesheet" href="css/meanmenu.min.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/main.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="css/calendar/fullcalendar.print.min.css">
    <!-- Style CSS
		============================================ -->
    <link rel="stylesheet" href="style.css">
    <!-- Responsive CSS
		============================================ -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Custom Style Sidebar CSS
		============================================ -->    
    <link rel="stylesheet" href="css/custom-style-sidebar.css">
    <!-- Custom Style Sidebar Fixed CSS
		============================================ -->
    <link rel="stylesheet" href="css/custom-style-sidebar-fixed.css">
    <!-- modernizr JS
		============================================ -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
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
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

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
    </div>
    <div class="left-custom-menu-adp-wrap comment-scrollbar">
      <nav class="sidebar-nav left-sidebar-menu-pro">
        <?php include('sidebar-nav-universal.php'); ?>
      </nav>
    </div>
  </nav>
</div>

    <!-- Start Welcome area -->
    <div class="all-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                </div>
            </div>
        </div>
        <div class="header-advance-area">
            <div class="header-top-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header-top-wraper">
                                <div class="row">
                                    <div class="col-lg-1 col-md-0 col-sm-1 col-xs-12">
                                        <div class="menu-switcher-pro">
                                            <button type="button" id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
                                            <i class="icon nalika-menu-task"></i>
                                          </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12">
                                        <div class="header-top-menu tabl-d-n hd-search-rp">
                                            <div class="breadcome-heading">
                                              <form role="search" class="">
                                                <input type="text" placeholder="Search..." class="form-control">
                                                <a href=""><i class="fa fa-search"></i></a>
                                              </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <div class="header-right-info">
                                          <button id="toggle-theme" class="btn btn-sm btn-outline-secondary" style="margin-right: 10px;">
  Light Mode
</button>

                                            <ul class="nav navbar-nav mai-top-nav header-right-menu">                                                      
                                                <li class="nav-item">
                                                    <a href="logout.php"> Log Out</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        <div class="section-admin container-fluid">
            <div class="row admin text-center">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                        <br><br>
                            <div class="admin-content analysis-progrebar-ctn res-mg-t-15">
                                <h4 class="text-left text-uppercase"><b>User</b></h4>
                                <div class="row vertical-center-box vertical-center-box-tablet">
                                    <div class="col-xs-3 mar-bot-15 text-left">
                                        <h1><i class="icon nalika-user icon-wrap" style="color:tomato;"></i></h1>
                                    </div>
                                    <div class="col-xs-9 cus-gh-hd-pro">
                                        <?php
                                            $query = mysqli_query($connect,"SELECT count(*) totaluser FROM users");
                                            $datauser = mysqli_fetch_array($query);
                                        ?>
                                        <h2 class="text-right no-margin"><?php echo $datauser['totaluser']; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12" style="margin-bottom:1px;">
                        <br><br>
                            <div class="admin-content analysis-progrebar-ctn res-mg-t-30">
                                <h4 class="text-left text-uppercase"><b>Enkripsi</b></h4>
                                <div class="row vertical-center-box vertical-center-box-tablet">
                                    <div class="text-left col-xs-3 mar-bot-15">
                                        <h1><i class="icon nalika-unlocked icon-wrap" style="color:tomato;"></i></h1>
                                    </div>
                                    <div class="col-xs-9 cus-gh-hd-pro">
                                        <?php
                                            $query = mysqli_query($connect,"SELECT count(*) totalencrypt FROM file WHERE status='1'");
                                            $dataencrypt = mysqli_fetch_array($query);
                                        ?>
                                        <h2 class="text-right no-margin"><?php echo $dataencrypt['totalencrypt']; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                        <br><br>
                            <div class="admin-content analysis-progrebar-ctn res-mg-t-30">
                                <h4 class="text-left text-uppercase"><b>Dekripsi</b></h4>
                                <div class="row vertical-center-box vertical-center-box-tablet">
                                    <div class="text-left col-xs-3 mar-bot-15">
                                        <h1><i class="icon nalika-unlocked icon-wrap" style="color:tomato;"></i></h1>
                                    </div>
                                    <div class="col-xs-9 cus-gh-hd-pro">
                                        <?php
                                            $query = mysqli_query($connect,"SELECT count(*) totaldecrypt FROM file WHERE status='2'");
                                            $datadecrypt = mysqli_fetch_array($query);
                                        ?>
                                        <h2 class="text-right no-margin"><?php echo $datadecrypt['totaldecrypt']; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<section class="breadcome-list mt-5">
  <div class="card bg-dark text-dark">
    <div class="card-body">
      <h4 class="text-info">Comparative Analysis of AES-128 and AES-256</h4>
      <div class="table-responsive mt-3">
        <table class="table table-bordered text-dark">
          <thead style="background-color:#e9ecef;">
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
            $queryAES = "SELECT * FROM file WHERE alg_used IS NOT NULL ORDER BY tgl_upload DESC";
            $resultAES = mysqli_query($connect, $queryAES);
            $no = 1;
            if (mysqli_num_rows($resultAES) === 0) {
      echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data analisis AES yang tersedia.</td></tr>";
    }
    while ($rowAES = mysqli_fetch_assoc($resultAES)) {
              echo "<tr>
                      <td>{$no}</td>
                      <td>" . htmlspecialchars($rowAES['file_name_source']) . "</td>
                      <td>" . htmlspecialchars($rowAES['alg_used']) . "</td>
                      <td>" . round($rowAES['file_size'], 2) . "</td>
                      <td>" . $rowAES['process_time_ms'] . "</td>
                      <td>" . ucfirst($rowAES['operation_type']) . "</td>
                      <td style='font-size: 0.8rem;'>" . substr($rowAES['hash_check'], 0, 12) . "...</td>
                      <td>" . date('Y-m-d H:i', strtotime($rowAES['tgl_upload'])) . "</td>
                    </tr>";
              $no++;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

        <section class="breadcome-list section-file-table">
        <div class="card">
            <div class="card-body">
              <div class="table-responsive" style="color:#fff;">
                <table id="file" class="table striped">
                  <thead>
                      <tr>
                        <!-- <td style="color:#fff;"><strong>ID File</strong></td> -->
                        <td style="color:#fff;"><strong>Username</strong></td>
                        <td style="color:#fff;"><strong>Nama File</strong></td>
                        <td style="color:#fff;"><strong>Nama File Enkripsi</strong></td>
                        <td style="color:#fff;"><strong>Ukuran File</strong></td>
                        <td style="color:#fff;"><strong>Tanggal</strong></td>
                        <td style="color:#fff;"><strong>Status</strong></td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $query = mysqli_query($connect,"SELECT * FROM file");
                        while ($data = mysqli_fetch_array($query)) { ?>
                        <tr>
                          <!-- <td><?php echo $data['id_file']; ?></td> -->
                          <td><?php echo $data['username']; ?></td>
                          <td><?php echo $data['file_name_source']; ?></td>
                          <td><?php echo $data['file_name_finish']; ?></td>
                          <td><?php echo $data['file_size']; ?> KB</td>
                          <td><?php echo $data['tgl_upload']; ?></td>
                          <td><?php if ($data['status'] == 1) {
                            echo "<span class='btn btn-danger'>Terenkripsi</span>";
                          }elseif ($data['status'] == 2) {
                            echo "<span class='btn btn-success'>Sudah Didekripsi</span>";
                          }else {
                            echo "<span class='btn btn-danger'>Status Tidak Diketahui</span>";
                          }
                          ?></td>
                        </tr>
                        <?php
                      } ?>
                  </tbody>
                </table>
            </div>
          </div>
        </section>
 
    </div>
    <!-- jquery
		============================================ -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="js/metisMenu/metisMenu.min.js"></script>
    <script src="js/metisMenu/metisMenu-active.js"></script>
    <!-- sparkline JS
		============================================ -->
    <script src="js/sparkline/jquery.sparkline.min.js"></script>
    <script src="js/sparkline/jquery.charts-sparkline.js"></script>
    <!-- calendar JS
		============================================ -->
    <script src="js/calendar/moment.min.js"></script>
    <script src="js/calendar/fullcalendar.min.js"></script>
    <script src="js/calendar/fullcalendar-active.js"></script>
	<!-- float JS
		============================================ -->
    <script src="js/flot/jquery.flot.js"></script>
    <script src="js/flot/jquery.flot.resize.js"></script>
    <script src="js/flot/curvedLines.js"></script>
    <script src="js/flot/flot-active.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
    <script src="../assets/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
        $('#file').dataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true,
          "order": [0, "asc"]
        });
        });
    </script>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.js"></script>
    <script src="../assets/js/essential-plugins.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/pace.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <!-- FIX: Sidebar auto expanded -->
<script>
  $(document).ready(function () {
    $('body').removeClass('mini-navbar');
    $('#sidebar').removeClass('mini-navbar');
  });
</script>
</body>
</html>