<?php
// db.php — database connection
// Include this file wherever you need database access

$host   = 'localhost';
$dbname = 'kuet_photo';
$user   = 'root';
$pass   = '';  // XAMPP default has no password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>