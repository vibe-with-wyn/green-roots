<?php
// Database configuration for InfinityFree
$host = 'sql211.infinityfree.com';
$dbname = 'if0_40752096_greenroots_db';
$username = 'if0_40752096';
$password = 'BKhAGIbyv6';

// Set timezone
date_default_timezone_set('Asia/Manila');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Log error instead of displaying it in production
    error_log("Database Connection Error: " . $e->getMessage());
    die("A database connection error occurred. Please try again later.");
}

// Security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // Only if using HTTPS
?>