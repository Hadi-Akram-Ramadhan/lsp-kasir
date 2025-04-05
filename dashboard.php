<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/activity_log.php';
require_once $root_path . 'helpers/cashier_shift.php';
require_once $root_path . 'helpers/reservation.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /kasirdoy/auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$user_name = $_SESSION['username'];

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
                                <a href="/kasirdoy/pages/users.php" class="text-decoration-none">
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
                                <a href="/kasirdoy/pages/reports.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-file-earmark-text text-success"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Laporan</h6>
                                                    <p class="text-muted small mb-0">Lihat laporan sistem</p>
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

                <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Menu Waiter</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="/kasirdoy/pages/orders.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-cart text-primary" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Pesanan</h6>
                                                    <p class="text-muted small mb-0">Kelola pesanan pelanggan</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="/kasirdoy/pages/reservations.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-calendar-check text-success"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Reservasi</h6>
                                                    <p class="text-muted small mb-0">Kelola reservasi meja</p>
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
                                <a href="/kasirdoy/pages/transactions.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-cash-stack text-primary"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Transaksi</h6>
                                                    <p class="text-muted small mb-0">Proses pembayaran</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="/kasirdoy/pages/shifts.php" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                                    <i class="bi bi-clock-history text-success"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Shift</h6>
                                                    <p class="text-muted small mb-0">Kelola shift kasir</p>
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
                        <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($activity_logs)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-activity text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada aktivitas</p>
                        </div>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($activity_logs as $log): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-2 rounded me-3">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1">
                                            <span
                                                class="fw-bold"><?php echo htmlspecialchars($log['username']); ?></span>
                                            <?php echo htmlspecialchars($log['description']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Cashier Shift Status -->
                <?php if ($user_role === 'kasir' && $active_shift): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Status Shift</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                <i class="bi bi-clock text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Shift Aktif</h6>
                                <p class="text-muted small mb-0">
                                    Mulai: <?php echo date('H:i', strtotime($active_shift['start_time'])); ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Transaksi:</span>
                            <span class="fw-bold"><?php echo $active_shift['total_transactions']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Penjualan:</span>
                            <span class="fw-bold">Rp
                                <?php echo number_format($active_shift['total_amount'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="mt-3">
                            <a href="/kasirdoy/pages/shifts.php" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-clock-history me-1"></i>Lihat Detail Shift
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Today's Reservations -->
                <?php if (!empty($today_reservations)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Reservasi Hari Ini</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($today_reservations as $reservation): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-calendar-check text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Meja <?php echo htmlspecialchars($reservation['table_number']); ?></h6>
                                <p class="text-muted small mb-0">
                                    <?php echo htmlspecialchars($reservation['customer_name']); ?> •
                                    <?php echo date('H:i', strtotime($reservation['reservation_time'])); ?>
                                </p>
                            </div>
                            <span class="badge bg-<?php 
                                echo $reservation['status'] === 'confirmed' ? 'success' : 
                                    ($reservation['status'] === 'pending' ? 'warning' : 'danger'); 
                            ?> rounded-pill">
                                <?php echo ucfirst($reservation['status']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                        <div class="mt-3">
                            <a href="/kasirdoy/pages/reservations.php" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-calendar-check me-1"></i>Lihat Semua Reservasi
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Notifications -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Notifikasi</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($unread_notifications > 0): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-bell me-2"></i>
                            Anda memiliki <?php echo $unread_notifications; ?> notifikasi baru
                        </div>
                        <div class="mt-3">
                            <a href="/kasirdoy/pages/notifications.php" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-bell me-1"></i>Lihat Notifikasi
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bell text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Tidak ada notifikasi baru</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>