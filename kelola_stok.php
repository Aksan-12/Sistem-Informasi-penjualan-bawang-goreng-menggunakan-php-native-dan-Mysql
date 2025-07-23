<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');

// Koneksi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bawang_db';
$port = 3307;
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Get current stock
    $query_current = "SELECT jumlah_stok FROM stok ORDER BY id DESC LIMIT 1";
    $result_current = $conn->query($query_current);
    $current_stock = 0;

    if ($result_current->num_rows > 0) {
        $current_stock = $result_current->fetch_assoc()['jumlah_stok'];
    }

    // Calculate new stock
    if ($jenis_transaksi == 'masuk') {
        $new_stock = $current_stock + $jumlah;
    } else {
        $new_stock = $current_stock - $jumlah;
        if ($new_stock < 0) {
            $message = '<div class="alert alert-danger">Error: Stok tidak mencukupi! Stok saat ini: ' . $current_stock . ' kg</div>';
        }
    }

    if ($new_stock >= 0) {
        // Update stok
        $update_query = "UPDATE stok SET jumlah_stok = '$new_stock' WHERE id = (SELECT id FROM (SELECT id FROM stok ORDER BY id DESC LIMIT 1) AS temp)";
        if ($conn->query($update_query)) {
            $message = '<div class="alert alert-success">Stok berhasil diperbarui! Stok ' . $jenis_transaksi . ' sebanyak ' . $jumlah . ' kg. Stok saat ini: ' . $new_stock . ' kg</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
    }
}

// Get current stock
$query_current = "SELECT jumlah_stok, tanggal_update FROM stok ORDER BY id DESC LIMIT 1";
$result_current = $conn->query($query_current);
$current_stock = 0;
$last_update = '';

if ($result_current->num_rows > 0) {
    $row = $result_current->fetch_assoc();
    $current_stock = $row['jumlah_stok'];
    $last_update = $row['tanggal_update'];
}

// Get stock history
$query_history = "SELECT *, jumlah_stok as stok_akhir FROM stok ORDER BY id DESC LIMIT 10";
$result_history = $conn->query($query_history);
$total_transaksi = $result_history->num_rows;

// Get total penjualan bulan ini
$total_terjual = 0;
$table_exists = $conn->query("SHOW TABLES LIKE 'penjualan'");
if ($table_exists->num_rows > 0) {
    $query_penjualan = "SELECT SUM(jumlah) as total_terjual FROM penjualan 
                       WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
    $result_penjualan = $conn->query($query_penjualan);
    if ($result_penjualan) {
        $penjualan = $result_penjualan->fetch_assoc();
        $total_terjual = $penjualan['total_terjual'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-card {
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stock-alert {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Mobile specific styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .stats-card .card-body {
                padding: 1rem;
            }

            .stats-card h3 {
                font-size: 1.5rem;
            }

            .stats-card h5 {
                font-size: 0.9rem;
            }

            .stats-card small {
                font-size: 0.7rem;
            }

            .stats-card .fa-2x {
                font-size: 1.5em !important;
            }

            /* Make form elements stack on mobile */
            .mobile-stack {
                flex-direction: column;
            }

            .mobile-stack .col-md-4 {
                margin-bottom: 1rem;
            }

            /* Improve table display on mobile */
            .table-responsive {
                border: none;
            }

            .table td,
            .table th {
                padding: 0.5rem 0.25rem;
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .table th {
                font-size: 0.8rem;
            }

            /* Hide some columns on very small screens */
            .hide-mobile {
                display: none;
            }

            /* Make cards more compact on mobile */
            .card {
                margin-bottom: 1rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }

            /* Adjust navbar for mobile */
            .navbar-brand {
                font-size: 0.9rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            /* Make alert messages more compact */
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            /* Improve form layout on mobile */
            .form-label {
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
            }

            .form-control,
            .form-select {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            /* Make buttons more touch-friendly */
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {

            /* Extra small screens */
            .stats-card .d-flex {
                flex-direction: column;
                text-align: center;
            }

            .stats-card .fa-2x {
                margin-bottom: 0.5rem;
                margin-right: 0 !important;
            }

            .table {
                font-size: 0.75rem;
            }

            .table td,
            .table th {
                padding: 0.25rem 0.1rem;
            }

            /* Stack form buttons vertically on very small screens */
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .text-muted {
                text-align: center;
                margin-bottom: 0.5rem;
            }
        }

        /* Improve touch targets */
        .btn,
        .form-control,
        .form-select {
            min-height: 44px;
        }

        /* Better spacing for mobile */
        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-arrow-left"></i>
                <span class="d-none d-sm-inline">Kembali ke Dashboard</span>
                <span class="d-inline d-sm-none">Dashboard</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-boxes"></i>
                    <span class="d-none d-sm-inline">Kelola Stok Bawang</span>
                    <span class="d-inline d-sm-none">Kelola Stok</span>
                </h2>
                <?php echo $message; ?>

                <!-- Stats Cards - Mobile responsive -->
                <div class="row mb-4">
                    <div class="col-12 col-md-4">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box-open fa-2x me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Stok Saat Ini</h5>
                                        <h3 class="mb-0 <?php echo $current_stock < 100 ? 'stock-alert' : ''; ?>">
                                            <?php echo number_format($current_stock); ?> kg
                                        </h3>
                                        <small>Update: <?php echo $last_update ? date('d/m/Y H:i', strtotime($last_update)) : 'Belum ada update'; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-history fa-2x me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Total Record</h5>
                                        <h3 class="mb-0"><?php echo $total_transaksi; ?></h3>
                                        <small>Riwayat stok</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line fa-2x me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Total Terjual</h5>
                                        <h3 class="mb-0"><?php echo number_format($total_terjual); ?> kg</h3>
                                        <small>Bulan ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($current_stock < 100): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Peringatan!</strong> Stok bawang sudah menipis (&lt; 100 kg). Segera lakukan pembelian ulang.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-edit"></i> Update Stok</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row mobile-stack">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Transaksi</label>
                                        <select class="form-select" name="jenis_transaksi" required>
                                            <option value="">Pilih...</option>
                                            <option value="masuk">Tambah Stok (Masuk)</option>
                                            <option value="keluar">Kurangi Stok (Keluar)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah (kg)</label>
                                        <input type="number" class="form-control" name="jumlah" min="1" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle"></i> Stok saat ini: <strong><?php echo number_format($current_stock); ?> kg</strong>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <span class="d-none d-sm-inline">Update Stok</span>
                                    <span class="d-inline d-sm-none">Update</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history"></i>
                            <span class="d-none d-sm-inline">Riwayat Stok (10 Record Terakhir)</span>
                            <span class="d-inline d-sm-none">Riwayat Stok</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="hide-mobile">ID</th>
                                        <th>Stok</th>
                                        <th class="d-none d-md-table-cell">Tanggal Update</th>
                                        <th class="d-md-none">Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_history->num_rows > 0): ?>
                                        <?php $no = 1;
                                        $previous_stock = null;
                                        while ($row = $result_history->fetch_assoc()):
                                            $current_row_stock = $row['jumlah_stok'];
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td class="hide-mobile"><?php echo $row['id']; ?></td>
                                                <td><strong><?php echo number_format($row['jumlah_stok']); ?> kg</strong></td>
                                                <td class="d-none d-md-table-cell"><?php echo date('d/m/Y H:i:s', strtotime($row['tanggal_update'])); ?></td>
                                                <td class="d-md-none"><?php echo date('d/m', strtotime($row['tanggal_update'])); ?></td>
                                                <td>
                                                    <?php if ($previous_stock !== null): ?>
                                                        <?php if ($current_row_stock > $previous_stock): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-arrow-up"></i>
                                                                <span class="d-none d-sm-inline">+<?php echo number_format($current_row_stock - $previous_stock); ?> kg</span>
                                                                <span class="d-inline d-sm-none">+<?php echo $current_row_stock - $previous_stock; ?></span>
                                                            </span>
                                                        <?php elseif ($current_row_stock < $previous_stock): ?>
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-arrow-down"></i>
                                                                <span class="d-none d-sm-inline">-<?php echo number_format($previous_stock - $current_row_stock); ?> kg</span>
                                                                <span class="d-inline d-sm-none">-<?php echo $previous_stock - $current_row_stock; ?></span>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-minus"></i>
                                                                <span class="d-none d-sm-inline">Tidak ada perubahan</span>
                                                                <span class="d-inline d-sm-none">-</span>
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-flag"></i>
                                                            <span class="d-none d-sm-inline">Terbaru</span>
                                                            <span class="d-inline d-sm-none">New</span>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $previous_stock = $current_row_stock;
                                        endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data stok</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }
            });
        }, 5000);

        document.querySelector('form').addEventListener('submit', function(e) {
            const jenisTransaksi = document.querySelector('select[name="jenis_transaksi"]').value;
            const jumlah = parseInt(document.querySelector('input[name="jumlah"]').value);
            const currentStock = <?php echo $current_stock; ?>;
            if (jenisTransaksi === 'keluar' && jumlah > currentStock) {
                e.preventDefault();
                alert('Jumlah yang akan dikeluarkan (' + jumlah + ' kg) melebihi stok saat ini (' + currentStock + ' kg)');
            }
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>