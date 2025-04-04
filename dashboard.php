<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$role = getCurrentUserRole();
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

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">KasirDoy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (in_array($role, ['administrator'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/tables.php">
                            <i class="bi bi-table"></i> Entri Meja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/products.php">
                            <i class="bi bi-box"></i> Entri Barang
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($role, ['waiter'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/products.php">
                            <i class="bi bi-box"></i> Entri Barang
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($role, ['waiter'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/orders.php">
                            <i class="bi bi-cart"></i> Entri Order
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($role, ['kasir'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/transactions.php">
                            <i class="bi bi-cash"></i> Entri Transaksi
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($role, ['waiter', 'kasir', 'owner'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/reports.php">
                            <i class="bi bi-file-text"></i> Generate Laporan
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            (<?php echo ucfirst($role); ?>)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p>Select a menu item from the navigation bar above to get started.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>