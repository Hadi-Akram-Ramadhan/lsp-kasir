<?php
/**
 * Activity Log Helper Functions
 */

/**
 * Log a new activity
 * 
 * @param PDO $conn Database connection
 * @param int $user_id User ID
 * @param string $action Action performed (login, logout, create, update, delete)
 * @param string $description Description of the activity
 * @return bool Success status
 */
function logActivity($conn, $user_id, $action, $description) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $action, $description]);
}

/**
 * Get recent activity logs
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of logs to retrieve
 * @return array Activity logs
 */
function getActivityLogs($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT al.*, u.username 
        FROM activity_logs al
        JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 