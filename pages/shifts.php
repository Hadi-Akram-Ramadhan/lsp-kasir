<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/cashier_shift.php';

// Check if user has kasir role
checkRole(['kasir']);

// Initialize message variables
$message = '';
$messageType = '';

// Handle shift actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'start') {
                startShift($conn, $_SESSION['user_id']);
                $message = "Shift dimulai!";
                $messageType = "success";
            } elseif ($_POST['action'] === 'end') {
                endShift($conn, $_POST['shift_id']);
                $message = "Shift selesai!";
                $messageType = "success";
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Get active shift
$active_shift = getActiveShift($conn, $_SESSION['user_id']);

// Get shift report for current month
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');
$shift_report = getShiftReport($conn, $start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Shift - KasirDoy</title>
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
                <!-- Active Shift -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Shift Aktif</h5>
                                <p class="text-muted mb-0">Status shift kerja saat ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($active_shift): ?>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-primary bg-opacity-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-clock text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="card-title mb-1">Mulai Shift</h6>
                                                <p class="mb-0">
                                                    <?php echo date('H:i', strtotime($active_shift['start_time'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-success bg-opacity-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-cash text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="card-title mb-1">Total Transaksi</h6>
                                                <p class="mb-0"><?php echo $active_shift['total_transactions']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-info bg-opacity-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-currency-dollar text-info" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="card-title mb-1">Total Penjualan</h6>
                                                <p class="mb-0">
                                                    Rp
                                                    <?php echo number_format($active_shift['total_amount'], 0, ',', '.'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="end">
                                <input type="hidden" name="shift_id" value="<?php echo $active_shift['id']; ?>">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-stop-circle me-2"></i>Selesai Shift
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clock text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada shift aktif</p>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="action" value="start">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-play-circle me-2"></i>Mulai Shift
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Shift Report -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Laporan Shift</h5>
                                <p class="text-muted mb-0">Riwayat shift bulan ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Durasi</th>
                                        <th>Transaksi</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($shift_report as $shift): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($shift['start_time'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($shift['start_time'])); ?></td>
                                        <td>
                                            <?php echo $shift['end_time'] ? date('H:i', strtotime($shift['end_time'])) : '-'; ?>
                                        </td>
                                        <td><?php echo $shift['duration'] ?? '-'; ?></td>
                                        <td><?php echo $shift['total_transactions']; ?></td>
                                        <td>Rp <?php echo number_format($shift['total_amount'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>