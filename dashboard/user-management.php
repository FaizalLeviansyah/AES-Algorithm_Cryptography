<?php
include('../auth.php');
include('../session.php');
include('../config.php');
if ($role != 'superadmin') {
  header('Location: unauthorized.php');
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Kelola User</title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include('menu.php'); ?>
<div class="all-content-wrapper p-4">
  <h2>Daftar Pengguna</h2>
  <table class="table table-bordered">
    <thead><tr><th>#</th><th>Username</th><th>Nama Lengkap</th><th>Role</th></tr></thead>
    <tbody>
    <?php
      $res = mysqli_query($connect, "SELECT * FROM users");
      $no = 1;
      while ($u = mysqli_fetch_array($res)) {
        echo "<tr>
          <td>{$no}</td>
          <td>{$u['username']}</td>
          <td>{$u['fullname']}</td>
          <td>{$u['role']}</td>
        </tr>";
        $no++;
      }
    ?>
    </tbody>
  </table>
</div>
</body>
</html>