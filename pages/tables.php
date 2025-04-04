<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';

// Check if user has administrator role
checkRole(['administrator']);

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $table_number = $_POST['table_number'];
                    
                    // Check if table number already exists
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM tables WHERE table_number = ?");
                    $stmt->execute([$table_number]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Nomor meja $table_number sudah ada!";
                        $messageType = "danger";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO tables (table_number) VALUES (?)");
                        $stmt->execute([$table_number]);
                        $message = "Meja berhasil ditambahkan!";
                        $messageType = "success";
                    }
                    break;

                case 'edit':
                    $id = $_POST['id'];
                    $table_number = $_POST['table_number']; 
                    $status = $_POST['status'];
                    
                    // Check if table number already exists for other tables
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM tables WHERE table_number = ? AND id != ?");
                    $stmt->execute([$table_number, $id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Nomor meja $table_number sudah ada!";
                        $messageType = "danger";
                    } else {
                        $stmt = $conn->prepare("UPDATE tables SET table_number = ?, status = ? WHERE id = ?");
                        $stmt->execute([$table_number, $status, $id]);
                        $message = "Meja berhasil diupdate!";
                        $messageType = "success";
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    
                    // Check if table is being used in any order
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE table_id = ? AND status = 'pending'");
                    $stmt->execute([$id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Meja tidak bisa dihapus karena sedang digunakan!";
                        $messageType = "danger";
                    } else {
                        $stmt = $conn->prepare("DELETE FROM tables WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = "Meja berhasil dihapus!";
                        $messageType = "success";
                    }
                    break;
            }
        } catch (Exception $e) {
            $message = "Terjadi kesalahan: " . $e->getMessage();
            $messageType = "danger";
        }
        header('Location: tables.php');
        exit();
    }
}

// Get all tables
$stmt = $conn->query("SELECT * FROM tables ORDER BY table_number");
$tables = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Meja - KasirDoy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container mt-4">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Meja</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="bi bi-plus"></i> Tambah Meja
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No. Meja</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                        <td>
                            <span
                                class="badge <?php echo $table['status'] === 'available' ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $table['status'] === 'available' ? 'Tersedia' : 'Terisi'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editTableModal<?php echo $table['id']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteTableModal<?php echo $table['id']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editTableModal<?php echo $table['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Meja</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?php echo $table['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Meja</label>
                                            <input type="text" class="form-control" name="table_number"
                                                value="<?php echo htmlspecialchars($table['table_number']); ?>"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="available"
                                                    <?php echo $table['status'] === 'available' ? 'selected' : ''; ?>>
                                                    Tersedia</option>
                                                <option value="occupied"
                                                    <?php echo $table['status'] === 'occupied' ? 'selected' : ''; ?>>
                                                    Terisi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteTableModal<?php echo $table['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Hapus Meja</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Yakin ingin menghapus meja nomor
                                        <?php echo htmlspecialchars($table['table_number']); ?>?</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $table['id']; ?>">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Table Modal -->
    <div class="modal fade" id="addTableModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Meja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Nomor Meja</label>
                            <input type="text" class="form-control" name="table_number" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>