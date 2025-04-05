<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/notification.php';

// Check if user has appropriate role
checkRole(['administrator', 'kasir', 'waiter', 'owner']);

// Initialize message variables
$message = '';
$messageType = '';

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read') {
    $notification_id = $_POST['notification_id'];
    markNotificationAsRead($conn, $notification_id);
    $message = "Notifikasi ditandai sudah dibaca";
    $messageType = "success";
}

// Get unread notifications
$notifications = getUnreadNotifications($conn);

// Check for low stock
$low_stock_items = checkLowStock($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - KasirDoy</title>
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
                <!-- Notifications -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Notifikasi</h5>
                                <p class="text-muted mb-0">Daftar notifikasi yang belum dibaca</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bell text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada notifikasi baru</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <?php if ($notif['type'] === 'order'): ?>
                                            <i class="bi bi-cart text-primary me-2"></i>
                                            <?php elseif ($notif['type'] === 'stock'): ?>
                                            <i class="bi bi-box text-warning me-2"></i>
                                            <?php else: ?>
                                            <i class="bi bi-info-circle text-info me-2"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($notif['message']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?>
                                        </small>
                                    </div>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="notification_id" value="<?php echo $notif['id']; ?>">
                                        <button type="submit" class="btn btn-light btn-sm">
                                            <i class="bi bi-check2 me-1"></i>Tandai Dibaca
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <?php if (!empty($low_stock_items)): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Peringatan Stok</h5>
                                <p class="text-muted mb-0">Produk dengan stok menipis</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Stok Minimum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-box me-2 text-warning"></i>
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger rounded-pill">
                                                <?php echo $item['stock']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $item['stock_minimum']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>