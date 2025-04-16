<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../auth/auth.php';

// Check if user has appropriate role
checkRole(['waiter', 'kasir', 'owner']);

// Initialize message variables
$message = '';
$messageType = '';

// Get date range from query parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get sales report
$stmt = $conn->prepare("
    SELECT 
        DATE(t.created_at) as date,
        COUNT(DISTINCT t.order_id) as total_orders,
        SUM(t.total_amount) as total_sales
    FROM transactions t
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY DATE(t.created_at)
    ORDER BY date DESC
");
$stmt->execute([$start_date, $end_date]);
$sales_report = $stmt->fetchAll();

// Get product report
$stmt = $conn->prepare("
    SELECT 
        p.name,
        SUM(oi.quantity) as total_quantity,
        SUM(oi.quantity * oi.price) as total_revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    JOIN transactions t ON o.id = t.order_id
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_quantity DESC
");
$stmt->execute([$start_date, $end_date]);
$product_report = $stmt->fetchAll();

// Get waiter performance report
$stmt = $conn->prepare("
    SELECT 
        u.username as waiter_name,
        COUNT(DISTINCT o.id) as total_orders,
        SUM(t.total_amount) as total_sales
    FROM orders o
    JOIN users u ON o.waiter_id = u.id
    JOIN transactions t ON o.id = t.order_id
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY u.id
    ORDER BY total_sales DESC
");
$stmt->execute([$start_date, $end_date]);
$waiter_report = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - KasirDoy</title>
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

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Filter Laporan</h5>
                                <p class="text-muted mb-0">Pilih rentang tanggal untuk melihat laporan</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date"
                                    value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>"
                                    required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Tampilkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sales Report -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Laporan Penjualan</h5>
                                <p class="text-muted mb-0">Ringkasan penjualan harian</p>
                            </div>
                            <div>
                                <a href="generate_pdf.php?type=sales&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-file-pdf me-1"></i>Generate PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Total Order</th>
                                        <th>Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sales_report as $row): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                        <td><?php echo $row['total_orders']; ?></td>
                                        <td>Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Report -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Laporan Produk</h5>
                                <p class="text-muted mb-0">Produk terlaris dan pendapatan</p>
                            </div>
                            <div>
                                <a href="generate_pdf.php?type=products&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-file-pdf me-1"></i>Generate PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Total Terjual</th>
                                        <th>Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($product_report as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-box me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo $row['total_quantity']; ?></td>
                                        <td>Rp <?php echo number_format($row['total_revenue'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waiter Report -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Laporan Waiter</h5>
                                <p class="text-muted mb-0">Performa waiter berdasarkan penjualan</p>
                            </div>
                            <div>
                                <a href="generate_pdf.php?type=waiters&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-file-pdf me-1"></i>Generate PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Waiter</th>
                                        <th>Total Order</th>
                                        <th>Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($waiter_report as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($row['waiter_name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo $row['total_orders']; ?></td>
                                        <td>Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
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