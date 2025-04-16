<?php
require_once 'auth/protect.php';

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';
require_once $root_path . 'helpers/activity_log.php';
require_once $root_path . 'helpers/cashier_shift.php';
require_once $root_path . 'helpers/reservation.php';

// Get user info from session
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;
$user_name = $_SESSION['username'] ?? null;

// Get recent activity logs
$activity_logs = getActivityLogs($conn, 5);

// Get active shift for cashier
$active_shift = null;
if ($user_role === 'kasir') {
    $active_shift = getActiveShift($conn, $user_id);
}

// Get today's reservations
$today = date('Y-m-d');
$today_reservations = getReservations($conn, $today);

// Get pending orders count
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$stmt->execute();
$pending_orders = $stmt->fetchColumn();

// Get low stock products (assuming low stock is less than 10)
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE stock < 10");
$stmt->execute();
$low_stock_count = $stmt->fetchColumn();

// Get unread notifications
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
$stmt->execute();
$unread_notifications = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KasirDoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include 'components/navbar.php'; ?>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Selamat datang, <?php echo htmlspecialchars($user_name); ?>!</h4>
                        <p class="text-muted">Dashboard KasirDoy - Sistem Manajemen Restoran</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Quick Stats -->
            <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Pesanan Pending</h6>
                                <h3 class="mb-0"><?php echo $pending_orders; ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-cart text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Stok Menipis</h6>
                                <h3 class="mb-0"><?php echo $low_stock_count; ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Reservasi Hari Ini</h6>
                                <h3 class="mb-0"><?php echo count($today_reservations); ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-calendar-check text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1">Notifikasi Baru</h6>
                                <h3 class="mb-0"><?php echo $unread_notifications; ?></h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-bell text-info" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8 mb-4">
                <!-- Role-specific content -->
                <?php if ($user_role === 'administrator'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Menu Administrator</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="pages/users.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Manajemen User</h6>
                                                    <p class="text-muted small mb-0">Kelola user sistem</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="pages/tables.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-table text-info" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Entri Meja</h6>
                                                    <p class="text-muted small mb-0">Kelola data meja</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="pages/products.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-box text-warning" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Entri Barang</h6>
                                                    <p class="text-muted small mb-0">Kelola data produk</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($user_role === 'waiter'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Menu Waiter</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="pages/products.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-box text-warning" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Entri Barang</h6>
                                                    <p class="text-muted small mb-0">Kelola data produk</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="pages/orders.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-cart text-success" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Entri Order</h6>
                                                    <p class="text-muted small mb-0">Kelola pesanan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="pages/reports.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-file-text text-primary"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Generate Laporan</h6>
                                                    <p class="text-muted small mb-0">Lihat laporan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($user_role === 'kasir'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Menu Kasir</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="pages/transactions.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-cash text-success" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Entri Transaksi</h6>
                                                    <p class="text-muted small mb-0">Kelola transaksi</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="pages/reports.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-file-text text-primary"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Generate Laporan</h6>
                                                    <p class="text-muted small mb-0">Lihat laporan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($user_role === 'owner'): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Menu Owner</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="pages/reports.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-file-text text-primary"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Generate Laporan</h6>
                                                    <p class="text-muted small mb-0">Lihat laporan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Activity Logs -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Aktivitas Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-list">
                            <?php foreach ($activity_logs as $log): ?>
                            <div class="activity-item d-flex align-items-start mb-3">
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        <span class="fw-bold"><?php echo htmlspecialchars($log['username']); ?></span>
                                        <small class="text-muted ms-2">
                                            <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo htmlspecialchars($log['description']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <?php if ($user_role === 'kasir'): ?>
                <!-- Active Shift Status -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Status Shift</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($active_shift): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Shift aktif sejak <?php echo date('H:i', strtotime($active_shift['start_time'])); ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Tidak ada shift aktif
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
                <!-- Today's Reservations -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Reservasi Hari Ini</h5>
                        <span class="badge bg-primary rounded-pill">
                            <?php echo count($today_reservations); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($today_reservations)): ?>
                        <p class="text-muted mb-0">Tidak ada reservasi hari ini</p>
                        <?php else: ?>
                        <div class="reservation-list">
                            <?php foreach ($today_reservations as $reservation): ?>
                            <div class="reservation-item mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-person me-2"></i>
                                    <strong><?php echo htmlspecialchars($reservation['customer_name']); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo date('H:i', strtotime($reservation['reservation_time'])); ?>
                                    </small>
                                    <span class="badge bg-<?php
                                        echo $reservation['status'] === 'confirmed' ? 'success' :
                                            ($reservation['status'] === 'pending' ? 'warning' : 'danger');
                                    ?> rounded-pill">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>