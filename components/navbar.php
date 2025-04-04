<?php
$role = getCurrentUserRole();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/kasirdoy/dashboard.php">KasirDoy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (in_array($role, ['administrator'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'tables.php' ? 'active' : ''; ?>"
                        href="/kasirdoy/pages/tables.php">
                        <i class="bi bi-table"></i> Entri Meja
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($role, ['administrator', 'waiter'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'products.php' ? 'active' : ''; ?>"
                        href="/kasirdoy/pages/products.php">
                        <i class="bi bi-box"></i> Entri Barang
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($role, ['waiter'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>"
                        href="/kasirdoy/pages/orders.php">
                        <i class="bi bi-cart"></i> Entri Order
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($role, ['kasir'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'transactions.php' ? 'active' : ''; ?>"
                        href="/kasirdoy/pages/transactions.php">
                        <i class="bi bi-cash"></i> Entri Transaksi
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($role, ['waiter', 'kasir', 'owner'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>"
                        href="/kasirdoy/pages/reports.php">
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
                    <a class="nav-link" href="/kasirdoy/auth/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>