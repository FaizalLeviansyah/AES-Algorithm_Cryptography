<?php
session_start();
include "../config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Analisis AES</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #ffffff;
      color: #ffffff;
    }
    .sidebar {
      background-color: #0f2b54;
      width: 230px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 1rem;
      color: black;
    }
    .sidebar a {
      color: black;
      display: block;
      margin: 10px 0;
      text-decoration: none;
    }
    .sidebar a:hover {
      color: #17a2b8;
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
    }
    .table thead {
      background-color: #004085;
    }
    .table td, .table th {
      color: black;
      vertical-align: middle;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <img src="../assets/img/logo.png" alt="Logo" style="width:100%; margin-bottom: 1rem;">
  <div class="text-center mb-3">
    <img src="../assets/img/avatar.svg" style="width:60px; border-radius:50%;"><br>
    <strong>Admin</strong><br><small class="text-success">admin</small>
  </div>
  <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="enkripsi.php"><i class="fas fa-lock"></i> Enkripsi</a>
  <a href="dekripsi.php"><i class="fas fa-unlock"></i> Dekripsi</a>
</div>

<div class="content">
  <h3 class="text-info">📊 Analisis Perbandingan AES-128 dan AES-256</h3>

  <table class="table table-bordered mt-4">
    <thead>
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
      $query = "SELECT * FROM file WHERE alg_used IS NOT NULL ORDER BY tgl_upload DESC";
      $result = mysqli_query($connect, $query);
      $no = 1;
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$no}</td>
                <td>" . htmlspecialchars($row['file_name_source']) . "</td>
                <td>" . htmlspecialchars($row['alg_used']) . "</td>
                <td>" . round($row['file_size'], 2) . "</td>
                <td>" . $row['process_time_ms'] . "</td>
                <td>" . ucfirst($row['operation_type']) . "</td>
                <td style='font-size: 0.8rem;'>" . substr($row['hash_check'], 0, 12) . "...</td>
                <td>" . date('Y-m-d H:i', strtotime($row['tgl_upload'])) . "</td>
              </tr>";
        $no++;
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>
