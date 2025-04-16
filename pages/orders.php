<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/activity_log.php';

// Check if user has appropriate role
checkRole(['administrator', 'waiter']);

// Get available tables
$stmt = $conn->query("SELECT * FROM tables WHERE status = 'available' ORDER BY table_number");
$available_tables = $stmt->fetchAll();

// Get all products
$stmt = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY name");
$available_products = $stmt->fetchAll();

// Get active orders for logged in user
$stmt = $conn->prepare("
    SELECT 
        o.*,
        t.table_number,
        GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN tables t ON o.table_id = t.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.waiter_id = ? AND o.status = 'pending'
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$active_orders = $stmt->fetchAll();

// Initialize message variables
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $conn->beginTransaction();
                    
                    // Create order
                    $table_id = $_POST['table_id'];
                    $waiter_id = $_SESSION['user_id'];
                    
                    $stmt = $conn->prepare("INSERT INTO orders (table_id, waiter_id) VALUES (?, ?)");
                    $stmt->execute([$table_id, $waiter_id]);
                    $order_id = $conn->lastInsertId();

                    // Update table status
                    $stmt = $conn->prepare("UPDATE tables SET status = 'occupied' WHERE id = ?");
                    $stmt->execute([$table_id]);

                    // Add order items
                    $products = $_POST['products'];
                    $quantities = $_POST['quantities'];
                    
                    for ($i = 0; $i < count($products); $i++) {
                        if ($quantities[$i] > 0) {
                            // Get product price
                            $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
                            $stmt->execute([$products[$i]]);
                            $product = $stmt->fetch();

                            // Insert order item
                            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$order_id, $products[$i], $quantities[$i], $product['price']]);

                            // Update stock
                            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                            $stmt->execute([$quantities[$i], $products[$i]]);
                        }
                    }

                    $conn->commit();
                    
                    // Log activity
                    $table_number = $available_tables[array_search($table_id, array_column($available_tables, 'id'))]['table_number'];
                    logActivity($conn, $_SESSION['user_id'], 'create', "Membuat order baru untuk Meja $table_number");

                } catch (Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }
                break;

            case 'complete':
                $order_id = $_POST['order_id'];
                $table_id = $_POST['table_id'];

                // Update order status
                $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
                $stmt->execute([$order_id]);

                // Update table status
                $stmt = $conn->prepare("UPDATE tables SET status = 'available' WHERE id = ?");
                $stmt->execute([$table_id]);

                // Log activity
                $table_number = $available_tables[array_search($table_id, array_column($available_tables, 'id'))]['table_number'];
                logActivity($conn, $_SESSION['user_id'], 'update', "Menyelesaikan order untuk Meja $table_number");
                break;

            case 'cancel':
                $order_id = $_POST['order_id'];
                $table_id = $_POST['table_id'];

                try {
                    $conn->beginTransaction();

                    // Get order items to restore stock
                    $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                    $stmt->execute([$order_id]);
                    $items = $stmt->fetchAll();

                    foreach ($items as $item) {
                        // Restore stock
                        $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                        $stmt->execute([$item['quantity'], $item['product_id']]);
                    }

                    // Update order status
                    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
                    $stmt->execute([$order_id]);

                    // Update table status
                    $stmt = $conn->prepare("UPDATE tables SET status = 'available' WHERE id = ?");
                    $stmt->execute([$table_id]);

                    $conn->commit();

                    // Log activity
                    $table_number = $available_tables[array_search($table_id, array_column($available_tables, 'id'))]['table_number'];
                    logActivity($conn, $_SESSION['user_id'], 'update', "Membatalkan order untuk Meja $table_number");

                } catch (Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }
                break;
        }
        header('Location: orders.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entri Order - KasirDoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include '../components/navbar.php'; ?>

    <div class="container py-4">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Order Aktif</h5>
                                <p class="text-muted mb-0">Kelola order yang sedang berjalan</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addOrderModal">
                                <i class="bi bi-plus-lg me-2"></i>Order Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_orders)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada order aktif</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($active_orders as $order): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <i class="bi bi-table me-2 text-primary"></i>
                                            Meja <?php echo htmlspecialchars($order['table_number']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <form method="POST" class="me-2">
                                            <input type="hidden" name="action" value="complete">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="table_id"
                                                value="<?php echo $order['table_id']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </button>
                                        </form>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="table_id"
                                                value="<?php echo $order['table_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle me-1"></i>Batal
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="card-text mb-0">
                                    <i class="bi bi-list-ul me-2 text-muted"></i>
                                    <?php echo htmlspecialchars($order['items']); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Order Modal -->
    <div class="modal fade" id="addOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-4">
                            <label class="form-label">Pilih Meja</label>
                            <select class="form-select" name="table_id" required>
                                <option value="">Pilih Meja...</option>
                                <?php foreach ($available_tables as $table): ?>
                                <option value="<?php echo $table['id']; ?>">
                                    Meja <?php echo htmlspecialchars($table['table_number']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Produk</label>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th style="width: 150px;">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($available_products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-box me-2 text-primary"></i>
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </div>
                                            </td>
                                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span
                                                    class="badge <?php echo $product['stock'] > 0 ? 'bg-success' : 'bg-danger'; ?> rounded-pill">
                                                    <?php echo $product['stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <input type="hidden" name="products[]"
                                                    value="<?php echo $product['id']; ?>">
                                                <input type="number" class="form-control" name="quantities[]" min="0"
                                                    max="<?php echo $product['stock']; ?>" value="0">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($message): ?>
        Swal.fire({
            icon: '<?php echo $messageType === 'success' ? 'success' : 'error'; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 2000
        });
        <?php endif; ?>
    </script>
</body>

</html>