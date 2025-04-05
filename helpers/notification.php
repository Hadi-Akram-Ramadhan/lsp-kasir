<?php
/**
 * Notification Helper Functions
 */

/**
 * Create a new notification
 * 
 * @param PDO $conn Database connection
 * @param string $type Notification type (order, stock, system)
 * @param string $message Notification message
 * @param string $link Optional link to related page
 * @return bool Success status
 */
function createNotification($conn, $type, $message, $link = null) {
    try {
        // Check if link column exists
        $stmt = $conn->query("SHOW COLUMNS FROM notifications LIKE 'link'");
        $linkColumnExists = $stmt->fetch() !== false;

        if ($linkColumnExists) {
            $stmt = $conn->prepare("INSERT INTO notifications (type, message, link, created_at) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([$type, $message, $link]);
        } else {
            $stmt = $conn->prepare("INSERT INTO notifications (type, message, created_at) VALUES (?, ?, NOW())");
            return $stmt->execute([$type, $message]);
        }
    } catch (PDOException $e) {
        // If error is about missing link column, try without it
        if ($e->getCode() == '42S22') {
            $stmt = $conn->prepare("INSERT INTO notifications (type, message, created_at) VALUES (?, ?, NOW())");
            return $stmt->execute([$type, $message]);
        }
        throw $e;
    }
}

/**
 * Get unread notifications
 * 
 * @param PDO $conn Database connection
 * @return array Unread notifications
 */
function getUnreadNotifications($conn) {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Mark notification as read
 * 
 * @param PDO $conn Database connection
 * @param int $id Notification ID
 * @return bool Success status
 */
function markNotificationAsRead($conn, $id) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Check for low stock products and create notifications
 * 
 * @param PDO $conn Database connection
 * @return array Low stock products
 */
function checkLowStock($conn) {
    // Get products with low stock (less than 10)
    $stmt = $conn->prepare("
        SELECT id, name, stock 
        FROM products 
        WHERE stock < 10
    ");
    $stmt->execute();
    $low_stock_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create notifications for each low stock product
    foreach ($low_stock_products as $product) {
        $message = "Stok {$product['name']} menipis (tersisa {$product['stock']})";
        $link = "/kasirdoy/pages/products.php";
        createNotification($conn, 'stock', $message, $link);
    }
    
    return $low_stock_products;
} 