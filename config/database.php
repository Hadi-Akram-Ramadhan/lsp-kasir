<?php
require_once __DIR__ . '/../helpers/error_handler.php';

$host = 'localhost';
$username = 'root';  // default XAMPP username
$password = '';      // default XAMPP password
$database = 'kasirdoy';

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $conn = new PDO("mysql:host=$host", $username, $password, $options);
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    $conn->exec($sql);
    
    // Connect to the specific database
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password, $options);

    // Test connection
    $conn->query("SELECT 1");
} catch(PDOException $e) {
    handleDatabaseError($e);
}

// Function to safely execute queries
function executeQuery($conn, $query, $params = []) {
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        handleDatabaseError($e);
    }
}

// Function to get single row
function fetchRow($conn, $query, $params = []) {
    $stmt = executeQuery($conn, $query, $params);
    return $stmt->fetch();
}

// Function to get multiple rows
function fetchAll($conn, $query, $params = []) {
    $stmt = executeQuery($conn, $query, $params);
    return $stmt->fetchAll();
}
?>