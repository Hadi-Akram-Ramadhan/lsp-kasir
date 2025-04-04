<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';

function login($username, $password) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

function checkRole($allowedRoles) {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit();
    }
    
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        header('Location: ../dashboard.php');
        exit();
    }
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}
?>