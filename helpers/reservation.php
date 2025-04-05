<?php
function createReservation($conn, $table_id, $customer_name, $customer_phone, $reservation_time, $party_size) {
    $stmt = $conn->prepare("
        INSERT INTO table_reservations 
        (table_id, customer_name, customer_phone, reservation_time, party_size)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$table_id, $customer_name, $customer_phone, $reservation_time, $party_size]);
    return $conn->lastInsertId();
}

function getReservations($conn, $date = null) {
    $sql = "
        SELECT r.*, t.table_number 
        FROM table_reservations r
        JOIN tables t ON r.table_id = t.id
    ";
    
    if ($date) {
        $sql .= " WHERE DATE(r.reservation_time) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$date]);
    } else {
        $stmt = $conn->query($sql);
    }
    
    return $stmt->fetchAll();
}

function updateReservationStatus($conn, $id, $status) {
    $stmt = $conn->prepare("
        UPDATE table_reservations 
        SET status = ?
        WHERE id = ?
    ");
    $stmt->execute([$status, $id]);
}

function checkTableAvailability($conn, $table_id, $reservation_time) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count
        FROM table_reservations
        WHERE table_id = ?
        AND reservation_time = ?
        AND status != 'cancelled'
    ");
    $stmt->execute([$table_id, $reservation_time]);
    return $stmt->fetch()['count'] == 0;
} 