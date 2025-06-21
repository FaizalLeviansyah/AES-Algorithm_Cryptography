<?php
// Include the database configuration
include 'config.php';

// Fetch divisions for the dropdown
$divisions = [];
$result = mysqli_query($koneksi, "SELECT * FROM divisions ORDER BY division_name");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $divisions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - AES Cryptography</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Create a New Account</h2>
        <p>Register as a Master User to decrypt files.</p>
        
        <form action="register-process.php" method="post">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="division_id">Division</label>
                <select name="division_id" id="division_id" class="form-control" required>
                    <option value="">-- Select Division --</option>
                    <?php foreach ($divisions as $division): ?>
                        <option value="<?php echo htmlspecialchars($division['id']); ?>">
                            <?php echo htmlspecialchars($division['division_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="mt-3">Already have an account? <a href="index.php">Login here</a></p>
        </form>
    </div>
</body>
</html>