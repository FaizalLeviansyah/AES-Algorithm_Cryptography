<?php
include('session.php');
include('config.php');
?>
<!DOCTYPE html>
<html>
<head><title>Dekripsi File</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php include('dashboard/menu.php'); ?>
<div class="all-content-wrapper p-4">
  <h2>Data File</h2>
  <table class="table">
    <thead><tr><th>#</th><th>File</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php
    $query = mysqli_query($connect,"SELECT * FROM file");
    $no = 1;
    while ($data = mysqli_fetch_array($query)) {
      echo "<tr><td>$no</td><td>{$data['file_name_finish']}</td><td>{$data['status']}</td><td>";
      if ($data['status'] == '1') {
        echo "<a href='decrypt-file.php?id_file={$data['id_file']}' class='btn btn-sm btn-warning'>Dekripsi</a>";
      } else {
        echo "<span class='text-muted'>Sudah didekripsi</span>";
      }
      echo "</td></tr>";
      $no++;
    }
    ?>
    </tbody>
  </table>
</div>
</body>
</html>