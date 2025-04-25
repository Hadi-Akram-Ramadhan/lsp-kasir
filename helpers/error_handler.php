<?php
function logError($error, $type = 'ERROR') {
    $log_file = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $message = "[$timestamp] [$type] $error\n";
    
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0777, true);
    }
    
    error_log($message, 3, $log_file);
}

function showUserFriendlyError($message = null) {
    $default_message = 'Terjadi kesalahan. Silakan coba lagi nanti.';
    $error_message = $message ?? $default_message;
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => $error_message]);
        exit;
    }
    
    echo "<div class='alert alert-danger'>$error_message</div>";
    exit;
}

function handleDatabaseError($e) {
    logError($e->getMessage(), 'DATABASE');
    showUserFriendlyError('Database error occurred');
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = "PHP Error [$errno]: $errstr in $errfile on line $errline";
    logError($error_message, 'PHP');
    return true;
});

set_exception_handler(function($e) {
    logError($e->getMessage(), 'EXCEPTION');
    showUserFriendlyError();
}); 