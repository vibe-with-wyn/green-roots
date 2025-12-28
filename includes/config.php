<?php
// Database configuration
$host = 'sql211.infinityfree.com';
$dbname = 'if0_40752096_greenroots_db';
$username = 'if0_40752096';
$password = 'BKhAGIbyv6';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>