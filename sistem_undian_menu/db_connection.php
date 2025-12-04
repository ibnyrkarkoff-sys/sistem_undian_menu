<?php
// Database Configuration for InfinityFree
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_40599536');
define('DB_PASS', 'hZhczXu84lQfyL');  // Replace with password from InfinityFree
define('DB_NAME', 'if0_40599536_undian');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
