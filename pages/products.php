<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/activity_log.php';

// Check if user has administrator or waiter role
checkRole(['administrator', 'waiter']);

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $name = $_POST['name'];
                    $price = $_POST['price'];
                    $stock = $_POST['stock'];

                    // Check if product name already exists
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
                    $stmt->execute([$name]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Produk dengan nama '$name' sudah ada!";
                        $messageType = "warning";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $price, $stock]);
                        
                        // Log activity
                        $price_formatted = number_format($price, 0, ',', '.');
                        logActivity($conn, $_SESSION['user_id'], 'create', "Menambahkan produk baru: {$name} (Rp {$price_formatted}, Stok: {$stock})");
                        
                        $message = "Produk berhasil ditambahkan!";
                        $messageType = "success";
                    }
                    break;

                case 'edit':
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $price = $_POST['price'];
                    $stock = $_POST['stock'];

                    // Check if product name already exists for other products
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ? AND id != ?");
                    $stmt->execute([$name, $id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Produk dengan nama '$name' sudah ada!";
                        $messageType = "danger";
                    } else {
                        // Get old product data for logging
                        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        $old_product = $stmt->fetch();

                        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?");
                        $stmt->execute([$name, $price, $stock, $id]);
                        
                        // Log activity
                        $changes = [];
                        if ($old_product['name'] !== $name) $changes[] = "nama dari {$old_product['name']} ke {$name}";
                        if ($old_product['price'] !== $price) {
                            $old_price = number_format($old_product['price'], 0, ',', '.');
                            $new_price = number_format($price, 0, ',', '.');
                            $changes[] = "harga dari Rp {$old_price} ke Rp {$new_price}";
                        }
                        if ($old_product['stock'] !== $stock) $changes[] = "stok dari {$old_product['stock']} ke {$stock}";
                        
                        if (!empty($changes)) {
                            logActivity($conn, $_SESSION['user_id'], 'update', "Mengubah produk {$name}: " . implode(', ', $changes));
                        }
                        
                        $message = "Produk berhasil diupdate!";
                        $messageType = "success";
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];

                    // Check if product is being used in any order
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
                    $stmt->execute([$id]);
                    if ($stmt->fetchColumn() > 0) {
                        $message = "Produk tidak bisa dihapus karena sudah digunakan dalam order!";
                        $messageType = "danger";
                    } else {
                        // Get product name for logging
                        $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        $product_name = $stmt->fetchColumn();

                        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        
                        // Log activity
                        logActivity($conn, $_SESSION['user_id'], 'delete', "Menghapus produk: {$product_name}");
                        
                        $message = "Produk berhasil dihapus!";
                        $messageType = "success";
                    }
                    break;
            }
        } catch (Exception $e) {
            $message = "Terjadi kesalahan: " . $e->getMessage();
            $messageType = "danger";
        }
        header('Location: products.php');
        exit();
    }
}

// Get all products
$stmt = $conn->query("SELECT * FROM products ORDER BY name");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - KasirDoy</title>
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
            <h2>Manajemen Produk</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editProductModal<?php echo $product['id']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteProductModal<?php echo $product['id']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editProductModal<?php echo $product['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Produk</label>
                                            <input type="text" class="form-control" name="name"
                                                value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Harga</label>
                                            <input type="number" class="form-control" name="price"
                                                value="<?php echo $product['price']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stok</label>
                                            <input type="number" class="form-control" name="stock"
                                                value="<?php echo $product['stock']; ?>" required>
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
                    <div class="modal fade" id="deleteProductModal<?php echo $product['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Hapus Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Yakin ingin menghapus produk <?php echo htmlspecialchars($product['name']); ?>?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stock" required>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Real-time validation for product name
        document.querySelector('input[name="name"]').addEventListener('input', function(e) {
            const name = e.target.value;
            if (name.length > 0) {
                fetch('check_product.php?name=' + encodeURIComponent(name))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Produk sudah ada!',
                                text: 'Nama produk ini sudah terdaftar',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            e.target.setCustomValidity('Produk sudah ada');
                        } else {
                            e.target.setCustomValidity('');
                        }
                    });
            }
        });

        // Show notification after form submission
        <?php if (isset($_POST['action'])): ?>
        Swal.fire({
            icon: '<?php echo $messageType === 'success' ? 'success' : 'warning'; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 2000
        });
        <?php endif; ?>
    </script>
</body>

</html>