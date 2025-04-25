<?php
function startShift($conn, $user_id) {
    $stmt = $conn->prepare("
        INSERT INTO cashier_shifts (user_id, start_time, status) 
        VALUES (?, NOW(), 'active')
    ");
    $stmt->execute([$user_id]);
    return $conn->lastInsertId();
}

function endShift($conn, $shift_id) {
    $stmt = $conn->prepare("
        UPDATE cashier_shifts 
        SET end_time = CURRENT_TIMESTAMP,
            status = 'closed'
        WHERE id = ?
    ");
    $stmt->execute([$shift_id]);
}

function getActiveShift($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT * FROM cashier_shifts 
        WHERE user_id = ? AND status = 'active'
        ORDER BY start_time DESC LIMIT 1
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function updateShiftStats($conn, $shift_id, $amount) {
    $stmt = $conn->prepare("
        UPDATE cashier_shifts 
        SET total_transactions = total_transactions + 1,
            total_amount = total_amount + ?
        WHERE id = ?
    ");
    $stmt->execute([$amount, $shift_id]);
}

function getShiftReport($conn, $start_date, $end_date) {
    $stmt = $conn->prepare("
        SELECT 
            cs.*,
            u.username,
            u.name,
            TIMEDIFF(cs.end_time, cs.start_time) as duration
        FROM cashier_shifts cs
        JOIN users u ON cs.user_id = u.id
        WHERE DATE(cs.start_time) >= ? 
        AND DATE(cs.start_time) <= ?
        ORDER BY cs.start_time DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    return $stmt->fetchAll();
} 