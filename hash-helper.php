<?php
// File: hash-helper.php (Temporary utility)

//password for each role.
//admin username : admin || password : superadmin1234 
//admin username : divisi || password : masterdivisi1234
//admin username : user || password : masteruser1234

// --- SET THE PASSWORD YOU WANT TO HASH HERE ---
$plainTextPassword = 'superadmin1234'; 
// ---------------------------------------------

$hashedPassword = password_hash($plainTextPassword, PASSWORD_DEFAULT);

echo "<h2>Password Hashing Utility</h2>";
echo "<p><strong>Plain-text password:</strong> " . htmlspecialchars($plainTextPassword) . "</p>";
echo "<p><strong>Generated Secure Hash:</strong></p>";
echo "<textarea readonly style='width: 100%; height: 80px;'>" . htmlspecialchars($hashedPassword) . "</textarea>";
echo "<p>Copy the hash above and use it to update the password in your 'users' database table.</p>";
?>