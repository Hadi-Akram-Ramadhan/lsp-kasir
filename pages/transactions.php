<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';

// Check if user has cashier role
checkRole(['kasir']);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'process_payment') {
        try {
            $conn->beginTransaction();

            $order_id = $_POST['order_id'];
            $payment_method = $_POST['payment_method'];
            $total_amount = $_POST['total_amount'];
            $cashier_id = $_SESSION['user_id'];

            // Create transaction
            $stmt = $conn->prepare("INSERT INTO transactions (order_id, cashier_id, total_amount, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $cashier_id, $total_amount, $payment_method]);

            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
            $stmt->execute([$order_id]);

            // Update table status
            $stmt = $conn->prepare("UPDATE tables SET status = 'available' WHERE id = (SELECT table_id FROM orders WHERE id = ?)");
            $stmt->execute([$order_id]);

            $conn->commit();
            header('Location: transactions.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}

// Get pending orders with details
$stmt = $conn->query("
    SELECT 
        o.id as order_id,
        o.created_at,
        t.table_number,
        u.username as waiter_name,
        SUM(oi.quantity * oi.price) as total_amount,
        GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ' x ', oi.price, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    JOIN users u ON o.waiter_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.status = 'pending'
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$pending_orders = $stmt->fetchAll();

// Get recent transactions
$stmt = $conn->prepare("
    SELECT 
        tr.*,
        t.table_number,
        u.username as waiter_name,
        GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ' x ', oi.price, ')') SEPARATOR ', ') as items
    FROM transactions tr
    JOIN orders o ON tr.order_id = o.id
    JOIN tables t ON o.table_id = t.id
    JOIN users u ON o.waiter_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE tr.cashier_id = ?
    GROUP BY tr.id
    ORDER BY tr.created_at DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$recent_transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - KasirDoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Pending Orders -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Menunggu Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pending_orders)): ?>
                            <p class="text-center text-muted">Tidak ada order yang menunggu pembayaran</p>
                        <?php else: ?>
                            <?php foreach ($pending_orders as $order): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="card-title mb-0">
                                                Meja <?php echo htmlspecialchars($order['table_number']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-1"><strong>Pelayan:</strong> <?php echo htmlspecialchars($order['waiter_name']); ?></p>
                                        <p class="mb-2"><strong>Items:</strong> <?php echo htmlspecialchars($order['items']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Total: Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></h5>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal<?php echo $order['order_id']; ?>">
                                                <i class="bi bi-cash"></i> Proses Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Modal -->
                                <div class="modal fade" id="paymentModal<?php echo $order['order_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Proses Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="process_payment">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <input type="hidden" name="total_amount" value="<?php echo $order['total_amount']; ?>">
                                                    
                                                    <p><strong>Meja:</strong> <?php echo htmlspecialchars($order['table_number']); ?></p>
                                                    <p><strong>Total:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Metode Pembayaran</label>
                                                        <select class="form-select" name="payment_method" required>
                                                            <option value="">Pilih metode pembayaran...</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="card">Card</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Transaksi Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_transactions)): ?>
                            <p class="text-center text-muted">Belum ada transaksi</p>
                        <?php else: ?>
                            <?php foreach ($recent_transactions as $transaction): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small>Meja <?php echo htmlspecialchars($transaction['table_number']); ?></small>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-1"><small><?php echo htmlspecialchars($transaction['items']); ?></small></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success"><?php echo strtoupper($transaction['payment_method']); ?></span>
                                            <strong>Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 