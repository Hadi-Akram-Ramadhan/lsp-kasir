<?php
/*ilangin ini kalo mau ilangin login kedua
require_once 'auth/protect.php';
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'config/database.php';
require_once $root_path . 'auth/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KasirDoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(45deg, #00b4db, #0083b0);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 400px;
        overflow: hidden;
    }

    .login-header {
        background: #fff;
        padding: 30px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    .login-header h3 {
        color: #0083b0;
        font-weight: 700;
        margin: 0;
    }

    .login-body {
        padding: 30px;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #eee;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #00b4db;
        box-shadow: none;
    }

    .form-label {
        color: #555;
        font-weight: 500;
    }

    .btn-login {
        background: linear-gradient(45deg, #00b4db, #0083b0);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 180, 219, 0.3);
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .input-group-text {
        background: transparent;
        border: 2px solid #eee;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }

    .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h3><i class="fas fa-cash-register me-2"></i>KasirDoy</h3>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-login text-white w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php if ($error): ?>
    Swal.fire({
        icon: 'error',
        title: '<?php echo $error; ?>',
        showConfirmButton: false,
        timer: 2000
    });
    <?php endif; ?>
    </script>
</body>

</html>