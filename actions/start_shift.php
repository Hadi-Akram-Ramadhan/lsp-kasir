<?php
require_once '../auth/protect.php';
require_once '../config/database.php';
require_once '../helpers/cashier_shift.php';
require_once '../helpers/activity_log.php';

// Only allow cashiers
if ($_SESSION['role'] !== 'kasir') {
    header('Location: ../dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Start the shift
$shift_id = startShift($conn, $user_id);

// Log the activity
logActivity($conn, $user_id, 'start_shift', 'Memulai shift kasir');

// Redirect back to dashboard
header('Location: ../dashboard.php');
exit; 