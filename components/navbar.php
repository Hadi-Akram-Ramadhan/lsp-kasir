<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: index.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['username'];
$user_role = $_SESSION['role'];

// Determine if we're in a subdirectory
$is_in_pages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$base_path = $is_in_pages ? '../' : '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_path; ?>dashboard.php">
            <i class="bi bi-shop me-2"></i>
            <span class="fw-bold">KasirDoy</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>dashboard.php">
                        <i class="bi bi-house-door me-1"></i>Dashboard
                    </a>
                </li>

                <?php if ($user_role === 'administrator'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'tables.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/tables.php">
                        <i class="bi bi-grid me-1"></i>Entri Meja
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['administrator', 'waiter'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'products.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/products.php">
                        <i class="bi bi-box me-1"></i>Entri Barang
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($user_role === 'waiter'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/orders.php">
                        <i class="bi bi-cart me-1"></i>Entri Order
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($user_role === 'kasir'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'transactions.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/transactions.php">
                        <i class="bi bi-cash-stack me-1"></i>Entri Transaksi
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['waiter', 'kasir', 'owner'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/reports.php">
                        <i class="bi bi-file-earmark-text me-1"></i>Generate Laporan
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'notifications.php' ? 'active' : ''; ?>"
                        href="<?php echo $base_path; ?>pages/notifications.php">
                        <i class="bi bi-bell me-1"></i>Notifikasi
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($user_name); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo $base_path; ?>logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    padding: 0.5rem 1rem;
}

.navbar-brand {
    font-size: 1.25rem;
}

.nav-link {
    padding: 0.5rem 1rem;
    transition: all 0.2s;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 0.25rem;
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 0.25rem;
}

.dropdown-toggle::after {
    display: none;
}

.dropdown-item {
    padding: 0.5rem 1rem;
}

.dropdown-item i {
    width: 1.25rem;
}
</style>