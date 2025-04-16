<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['number'])) {
    $number = $_GET['number'];
    
    // Check if table number exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tables WHERE table_number = ?");
    $stmt->execute([$number]);
    $exists = $stmt->fetchColumn() > 0;
    
    echo json_encode(['exists' => $exists]);
}
?>