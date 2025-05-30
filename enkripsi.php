<?php
include('session.php');
include('config.php');
if ($role == 'reviewer') {
  header('Location: unauthorized.php');
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Enkripsi File</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php include('dashboard/menu.php'); ?>
<div class="all-content-wrapper p-4">
  <h2>Form Enkripsi</h2>
  <form method="post" action="encrypt-process.php" enctype="multipart/form-data">
    <div class="form-group">
      <label>File</label>
      <input type="file" name="file" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Password / Key</label>
      <input type="password" name="pwdfile" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="desc" class="form-control"></textarea>
    </div>
    <div class="form-group">
      <label>Pilih Algoritma</label>
      <select name="algorithm" class="form-control">
        <option value="AES-128">AES-128</option>
        <option value="AES-256">AES-256</option>
      </select>
    </div>
    <button class="btn btn-primary" name="encrypt_now" type="submit">Enkripsi Sekarang</button>
  </form>
</div>
</body>
</html>