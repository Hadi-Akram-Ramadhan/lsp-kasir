<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Hardcoded credentials (ganti ini dengan username dan password lo)
$VALID_USERNAME = "hadi";
$VALID_PASSWORD = "lsp2024"; // Ganti password ini ya!

// Cek kalo udah login
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Kalo belum login, redirect ke login page
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
    
    // Tampilin form login
    ?>
<!DOCTYPE html>
<html>

<head>
    <title>Login Required</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Login Required</h3>
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
    exit;
} else {
    // Kalo udah login, tampilin tombol logout
    ?>
<div class="position-fixed top-0 end-0 p-3">
    <a href="logout.php" class="btn btn-danger btn-sm">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>
<?php
} 