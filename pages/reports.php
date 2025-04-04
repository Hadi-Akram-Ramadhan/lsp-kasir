<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';

// Check if user has appropriate role
checkRole(['waiter', 'kasir', 'owner']);

// Get date range from query parameters
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get report type
$report_type = $_GET['type'] ?? 'sales';

// Function to get sales report
function getSalesReport($conn, $start_date, $end_date) {
    $stmt = $conn->prepare("
        SELECT 
            DATE(tr.created_at) as date,
            COUNT(DISTINCT tr.id) as total_transactions,
            SUM(tr.total_amount) as total_sales,
            COUNT(DISTINCT o.id) as total_orders,
            SUM(oi.quantity) as total_items
        FROM transactions tr
        JOIN orders o ON tr.order_id = o.id
        JOIN order_items oi ON o.id = oi.order_id
        WHERE DATE(tr.created_at) BETWEEN ? AND ?
        GROUP BY DATE(tr.created_at)
        ORDER BY date DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    return $stmt->fetchAll();
}

// Function to get product sales report
function getProductReport($conn, $start_date, $end_date) {
    $stmt = $conn->prepare("
        SELECT 
            p.name as product_name,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.quantity * oi.price) as total_sales
        FROM products p
        JOIN order_items oi ON p.id = oi.product_id
        JOIN orders o ON oi.order_id = o.id
        JOIN transactions tr ON o.id = tr.order_id
        WHERE DATE(tr.created_at) BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_sales DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    return $stmt->fetchAll();
}

// Function to get waiter performance report
function getWaiterReport($conn, $start_date, $end_date) {
    $stmt = $conn->prepare("
        SELECT 
            u.username as waiter_name,
            COUNT(DISTINCT o.id) as total_orders,
            SUM(oi.quantity) as total_items,
            SUM(tr.total_amount) as total_sales
        FROM users u
        JOIN orders o ON u.id = o.waiter_id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN transactions tr ON o.id = tr.order_id
        WHERE u.role = 'waiter'
        AND DATE(tr.created_at) BETWEEN ? AND ?
        GROUP BY u.id
        ORDER BY total_sales DESC
    ");
    $stmt->execute([$start_date, $end_date]);
    return $stmt->fetchAll();
}

// Get report data based on type
switch ($report_type) {
    case 'products':
        $report_data = getProductReport($conn, $start_date, $end_date);
        break;
    case 'waiters':
        $report_data = getWaiterReport($conn, $start_date, $end_date);
        break;
    default:
        $report_data = getSalesReport($conn, $start_date, $end_date);
        break;
}
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

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $report_type === 'sales' ? 'active' : ''; ?>"
                            href="?type=sales&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">
                            Laporan Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $report_type === 'products' ? 'active' : ''; ?>"
                            href="?type=products&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">
                            Laporan Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $report_type === 'waiters' ? 'active' : ''; ?>"
                            href="?type=waiters&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">
                            Laporan Pelayan
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <!-- Date Range Filter -->
                <form class="row g-3 mb-4">
                    <input type="hidden" name="type" value="<?php echo $report_type; ?>">
                    <div class="col-auto">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Filter</button>
                    </div>
                </form>

                <!-- Report Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <?php if ($report_type === 'sales'): ?>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Transaksi</th>
                                <th>Total Order</th>
                                <th>Total Item</th>
                                <th>Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                <td><?php echo $row['total_transactions']; ?></td>
                                <td><?php echo $row['total_orders']; ?></td>
                                <td><?php echo $row['total_items']; ?></td>
                                <td>Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                        <?php elseif ($report_type === 'products'): ?>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Total Terjual</th>
                                <th>Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo $row['total_quantity']; ?></td>
                                <td>Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                        <?php elseif ($report_type === 'waiters'): ?>
                        <thead>
                            <tr>
                                <th>Pelayan</th>
                                <th>Total Order</th>
                                <th>Total Item</th>
                                <th>Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['waiter_name']); ?></td>
                                <td><?php echo $row['total_orders']; ?></td>
                                <td><?php echo $row['total_items']; ?></td>
                                <td>Rp <?php echo number_format($row['total_sales'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>