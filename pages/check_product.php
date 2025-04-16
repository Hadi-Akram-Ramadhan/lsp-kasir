<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['name'])) {
    $name = $_GET['name'];
    
    // Check if product name exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
    $stmt->execute([$name]);
    $exists = $stmt->fetchColumn() > 0;
    
    echo json_encode(['exists' => $exists]);
}
?> 