<?php
session_start();
require_once 'config.php';

// Skip protection if disabled
if (!PROTECTION_ENABLED) {
    // Set default session values when protection is disabled
    $_SESSION['authenticated'] = true;
    return;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Cek kalo udah login
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Kalo belum login, redirect ke login page
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $admin = $_POST['admin'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($admin === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['authenticated'] = true;
            $_SESSION['admin'] = $admin;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "admin atau password salah!";
        }
    }
    
    // Tampilin form login
    ?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        width: 400px;
    }

    .login-header {
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        color: white;
        padding: 20px;
        border-radius: 15px 15px 0 0;
        text-align: center;
    }

    .login-body {
        padding: 30px;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #2d2d2d;
        box-shadow: 0 0 0 0.2rem rgba(45, 45, 45, 0.25);
    }

    .btn-login {
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .alert {
        border-radius: 8px;
    }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <h3><i class="fas fa-lock me-2"></i>Hadooyy Apa Bukan?</h3>
        </div>
        <div class="login-body">
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">admin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="admin" class="form-control" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-login text-white w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
    </div>
</body>

</html>
<?php
    exit;
} else {
    // Kalo udah login, tampilin tombol logout
    ?>

<?php
} 