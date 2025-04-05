<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/export.php';

// Check if user has administrator role
checkRole(['administrator']);

// Initialize message variables
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                // Check if username already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$_POST['username']]);
                if ($stmt->fetch()) {
                    $message = "Username sudah digunakan!";
                    $messageType = "danger";
                } else {
                    // Insert new user
                    $stmt = $conn->prepare("
                        INSERT INTO users (username, password, role) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['username'],
                        password_hash($_POST['password'], PASSWORD_DEFAULT),
                        $_POST['role']
                    ]);
                    
                    $message = "User berhasil ditambahkan!";
                    $messageType = "success";
                }
            } elseif ($_POST['action'] === 'edit') {
                // Check if username exists for other users
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$_POST['username'], $_POST['user_id']]);
                if ($stmt->fetch()) {
                    $message = "Username sudah digunakan!";
                    $messageType = "danger";
                } else {
                    // Update user
                    $sql = "UPDATE users SET username = ?, role = ?";
                    $params = [$_POST['username'], $_POST['role']];
                    
                    // Update password if provided
                    if (!empty($_POST['password'])) {
                        $sql .= ", password = ?";
                        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    }
                    
                    $sql .= " WHERE id = ?";
                    $params[] = $_POST['user_id'];
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    
                    $message = "User berhasil diupdate!";
                    $messageType = "success";
                }
            } elseif ($_POST['action'] === 'delete') {
                // Check if user has any orders
                $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE waiter_id = ?");
                $stmt->execute([$_POST['user_id']]);
                if ($stmt->fetchColumn() > 0) {
                    $message = "User tidak bisa dihapus karena memiliki pesanan!";
                    $messageType = "danger";
                } else {
                    // Delete user
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$_POST['user_id']]);
                    
                    $message = "User berhasil dihapus!";
                    $messageType = "success";
                }
            } elseif ($_POST['action'] === 'export') {
                // Get filtered users
                $users = getFilteredUsers($conn, $_POST['role_filter'] ?? null, $_POST['search'] ?? '');
                
                // Format data for export
                $exportData = [];
                $exportData[] = ['Username', 'Username', 'Role', 'Tanggal Dibuat'];
                
                foreach ($users as $user) {
                    $exportData[] = [
                        $user['username'],
                        $user['username'],
                        ucfirst($user['role']),
                        date('d/m/Y H:i', strtotime($user['created_at']))
                    ];
                }
                
                // Export to CSV
                exportToCSV($exportData, 'users_' . date('Y-m-d') . '.csv');
                exit;
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Get filter parameters
$role_filter = $_GET['role'] ?? null;
$search = $_GET['search'] ?? '';

// Get filtered users
$users = getFilteredUsers($conn, $role_filter, $search);

// Function to get filtered users
function getFilteredUsers($conn, $role = null, $search = '') {
    $sql = "SELECT * FROM users WHERE 1=1";
    $params = [];
    
    if ($role) {
        $sql .= " AND role = ?";
        $params[] = $role;
    }
    
    if ($search) {
        $sql .= " AND (username LIKE ?)";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - KasirDoy</title>
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
            <div class="col-md-10">
                <!-- Users -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Manajemen User</h5>
                                <p class="text-muted mb-0">Kelola user sistem</p>
                            </div>
                            <div class="d-flex">
                                <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="bi bi-download me-2"></i>Export
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addUserModal">
                                    <i class="bi bi-plus-lg me-2"></i>Tambah User
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" class="d-flex">
                                    <div class="input-group me-2">
                                        <input type="text" class="form-control" name="search"
                                            placeholder="Cari username atau nama..."
                                            value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <select class="form-select" name="role" style="width: auto;">
                                        <option value="">Semua Role</option>
                                        <option value="administrator"
                                            <?php echo $role_filter === 'administrator' ? 'selected' : ''; ?>>
                                            Administrator</option>
                                        <option value="kasir" <?php echo $role_filter === 'kasir' ? 'selected' : ''; ?>>
                                            Kasir</option>
                                        <option value="waiter"
                                            <?php echo $role_filter === 'waiter' ? 'selected' : ''; ?>>Waiter</option>
                                        <option value="owner" <?php echo $role_filter === 'owner' ? 'selected' : ''; ?>>
                                            Owner</option>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-6 text-end">
                                <?php if ($search || $role_filter): ?>
                                <a href="users.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle me-1"></i>Reset Filter
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>Role</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-search text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">Tidak ada user yang ditemukan</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $user['role'] === 'administrator' ? 'danger' : 
                                                    ($user['role'] === 'kasir' ? 'success' : 
                                                    ($user['role'] === 'waiter' ? 'primary' : 'warning')); 
                                            ?> rounded-pill">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal<?php echo $user['id']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">Pilih Role...</option>
                                <option value="administrator">Administrator</option>
                                <option value="kasir">Kasir</option>
                                <option value="waiter">Waiter</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="user_id" id="edit_user_id">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" name="password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="edit_role" required>
                                <option value="">Pilih Role...</option>
                                <option value="administrator">Administrator</option>
                                <option value="kasir">Kasir</option>
                                <option value="waiter">Waiter</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="export">

                        <div class="mb-3">
                            <label class="form-label">Filter Role</label>
                            <select class="form-select" name="role_filter">
                                <option value="">Semua Role</option>
                                <option value="administrator">Administrator</option>
                                <option value="kasir">Kasir</option>
                                <option value="waiter">Waiter</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter Pencarian</label>
                            <input type="text" class="form-control" name="search"
                                placeholder="Cari username atau nama...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Export CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Handle edit user modal
    document.getElementById('editUserModal<?php echo $user['id']; ?>').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const user = JSON.parse(button.getAttribute('data-user'));

        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_role').value = user.role;
    });
    </script>
</body>

</html>