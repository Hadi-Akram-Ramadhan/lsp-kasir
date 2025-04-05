<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root_path = $_SERVER['DOCUMENT_ROOT'] . '/kasirdoy/';
require_once $root_path . 'auth/auth.php';
require_once $root_path . 'helpers/reservation.php';

// Check if user has appropriate role
checkRole(['administrator', 'waiter']);

// Initialize message variables
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                // Validate table availability
                if (checkTableAvailability($conn, $_POST['table_id'], $_POST['reservation_time'])) {
                    createReservation(
                        $conn,
                        $_POST['table_id'],
                        $_POST['customer_name'],
                        $_POST['customer_phone'],
                        $_POST['reservation_time'],
                        $_POST['party_size']
                    );
                    $message = "Reservasi berhasil dibuat!";
                    $messageType = "success";
                } else {
                    $message = "Meja sudah direservasi untuk waktu tersebut!";
                    $messageType = "danger";
                }
            } elseif ($_POST['action'] === 'update_status') {
                updateReservationStatus($conn, $_POST['reservation_id'], $_POST['status']);
                $message = "Status reservasi berhasil diupdate!";
                $messageType = "success";
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Get available tables
$stmt = $conn->query("SELECT * FROM tables WHERE status = 'available' ORDER BY table_number");
$available_tables = $stmt->fetchAll();

// Get reservations for today
$today = date('Y-m-d');
$reservations = getReservations($conn, $today);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Meja - KasirDoy</title>
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
            <div class="col-md-8">
                <!-- Reservations -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Reservasi Hari Ini</h5>
                                <p class="text-muted mb-0">Daftar reservasi meja untuk hari ini</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                                <i class="bi bi-plus-lg me-2"></i>Reservasi Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reservations)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada reservasi untuk hari ini</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <i class="bi bi-table me-2 text-primary"></i>
                                            Meja <?php echo htmlspecialchars($reservation['table_number']); ?>
                                        </h6>
                                        <p class="card-text mb-1">
                                            <i class="bi bi-person me-2 text-muted"></i>
                                            <?php echo htmlspecialchars($reservation['customer_name']); ?> •
                                            <i class="bi bi-telephone me-2 text-muted"></i>
                                            <?php echo htmlspecialchars($reservation['customer_phone']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('H:i', strtotime($reservation['reservation_time'])); ?> •
                                            <i class="bi bi-people me-1"></i>
                                            <?php echo $reservation['party_size']; ?> orang
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <?php if ($reservation['status'] === 'pending'): ?>
                                        <form method="POST" class="me-2">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle me-1"></i>Konfirmasi
                                            </button>
                                        </form>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle me-1"></i>Batal
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="badge <?php echo $reservation['status'] === 'confirmed' ? 'bg-success' : 'bg-danger'; ?> rounded-pill">
                                            <?php echo ucfirst($reservation['status']); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reservation Modal -->
    <div class="modal fade" id="addReservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label class="form-label">Pilih Meja</label>
                            <select class="form-select" name="table_id" required>
                                <option value="">Pilih Meja...</option>
                                <?php foreach ($available_tables as $table): ?>
                                <option value="<?php echo $table['id']; ?>">
                                    Meja <?php echo htmlspecialchars($table['table_number']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" name="customer_phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu Reservasi</label>
                            <input type="datetime-local" class="form-control" name="reservation_time" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Orang</label>
                            <input type="number" class="form-control" name="party_size" min="1" required>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 