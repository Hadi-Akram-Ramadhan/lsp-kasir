<?php
$host = 'localhost';
$username = 'root';  // default XAMPP username
$password = '';      // default XAMPP password
$database = 'kasirdoy';

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    $conn->exec($sql);
    
    // Connect to the specific database
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>