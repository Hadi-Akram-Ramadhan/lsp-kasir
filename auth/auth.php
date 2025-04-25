<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';
require_once __DIR__ . '/../helpers/error_handler.php';

function login($username, $password) {
    global $conn;
    
    if (!checkLoginAttempts($username)) {
        logError("Too many login attempts for user: $username", 'LOGIN');
        throw new Exception('Too many failed login attempts. Please try again later.');
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            recordLoginAttempt($username, true);
            logError("Successful login for user: $username", 'LOGIN');
            return true;
        }
        
        recordLoginAttempt($username);
        return false;
    } catch(PDOException $e) {
        handleDatabaseError($e);
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function logout() {
    session_destroy();
    session_start();
    session_regenerate_id(true);
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

function checkLoginAttempts($username) {
    $attempts_file = __DIR__ . '/../logs/login_attempts.json';
    $max_attempts = 5;
    $lockout_time = 900; // 15 minutes
    
    if (!file_exists($attempts_file)) {
        file_put_contents($attempts_file, '{}');
    }
    
    $attempts = json_decode(file_get_contents($attempts_file), true);
    
    if (isset($attempts[$username])) {
        $attempt = $attempts[$username];
        if ($attempt['count'] >= $max_attempts && time() - $attempt['last_attempt'] < $lockout_time) {
            return false;
        }
        if (time() - $attempt['last_attempt'] >= $lockout_time) {
            unset($attempts[$username]);
        }
    }
    
    return true;
}

function recordLoginAttempt($username, $success = false) {
    $attempts_file = __DIR__ . '/../logs/login_attempts.json';
    $attempts = json_decode(file_get_contents($attempts_file), true);
    
    if ($success) {
        unset($attempts[$username]);
    } else {
        if (!isset($attempts[$username])) {
            $attempts[$username] = ['count' => 0, 'last_attempt' => 0];
        }
        $attempts[$username]['count']++;
        $attempts[$username]['last_attempt'] = time();
    }
    
    file_put_contents($attempts_file, json_encode($attempts));
}

// Check session timeout (30 minutes)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        logout();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Validate user role
function hasPermission($required_role) {
    $role_hierarchy = [
        'administrator' => 4,
        'owner' => 3,
        'kasir' => 2,
        'waiter' => 1
    ];
    
    $user_role = $_SESSION['role'] ?? '';
    $user_level = $role_hierarchy[$user_role] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;
    
    return $user_level >= $required_level;
}
?>