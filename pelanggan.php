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

// Query untuk data pelanggan dari tabel penjualan
$query = "SELECT 
    pembeli as nama_pelanggan,
    COUNT(*) as total_transaksi,
    SUM(jumlah) as total_pembelian,
    SUM(jumlah * harga) as total_pengeluaran,
    MAX(tanggal) as terakhir_beli,
    MIN(tanggal) as pertama_beli
    FROM penjualan 
    GROUP BY pembeli 
    ORDER BY total_pengeluaran DESC";
$result = $conn->query($query);

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $query = "SELECT 
        pembeli as nama_pelanggan,
        COUNT(*) as total_transaksi,
        SUM(jumlah) as total_pembelian,
        SUM(jumlah * harga) as total_pengeluaran,
        MAX(tanggal) as terakhir_beli,
        MIN(tanggal) as pertama_beli
        FROM penjualan 
        WHERE pembeli LIKE '%$search%'
        GROUP BY pembeli 
        ORDER BY total_pengeluaran DESC";
    $result = $conn->query($query);
}

// Statistik pelanggan
$query_stats = "SELECT 
    COUNT(DISTINCT pembeli) as total_pelanggan,
    AVG(jumlah * harga) as rata_rata_pembelian,
    COUNT(*) as total_transaksi_all
    FROM penjualan";
$result_stats = $conn->query($query_stats);
$stats = $result_stats->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-card {
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .customer-badge {
            font-size: 0.8em;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 0.9rem;
            }

            .stats-card .card-body h4 {
                font-size: 1.5rem;
            }

            .stats-card .card-body p {
                font-size: 0.8rem;
            }

            .stats-card .fa-2x {
                font-size: 1.5em !important;
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            .btn-sm {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }

            .card-header h4 {
                font-size: 1.2rem;
            }

            .customer-badge {
                font-size: 0.7em;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            .navbar-brand {
                font-size: 0.8rem;
            }

            .stats-card .card-body {
                padding: 1rem 0.5rem;
            }

            .stats-card .card-body h4 {
                font-size: 1.2rem;
            }

            .stats-card .card-body p {
                font-size: 0.7rem;
                margin-bottom: 0;
            }

            .table-responsive {
                font-size: 0.7rem;
            }

            .table td,
            .table th {
                padding: 0.3rem;
                vertical-align: middle;
            }

            .btn-sm {
                font-size: 0.6rem;
                padding: 0.2rem 0.4rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .search-container {
                gap: 0.5rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Improved table for mobile */
        @media (max-width: 768px) {
            .table-mobile-stack {
                display: block;
            }

            .table-mobile-stack thead {
                display: none;
            }

            .table-mobile-stack tbody,
            .table-mobile-stack tr,
            .table-mobile-stack td {
                display: block;
                width: 100%;
            }

            .table-mobile-stack tr {
                border: 1px solid #dee2e6;
                margin-bottom: 1rem;
                border-radius: 0.375rem;
                padding: 0.75rem;
                background: white;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .table-mobile-stack td {
                border: none;
                padding: 0.25rem 0;
                text-align: left !important;
            }

            .table-mobile-stack td:before {
                content: attr(data-label) ": ";
                font-weight: bold;
                color: #495057;
            }
        }

        /* Utility classes for responsive spacing */
        .mb-mobile {
            margin-bottom: 1rem;
        }

        @media (min-width: 768px) {
            .mb-mobile {
                margin-bottom: 0;
            }
        }

        .text-nowrap-mobile {
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .text-nowrap-mobile {
                white-space: normal;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid px-2 px-md-3">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-arrow-left"></i>
                <span class="d-none d-sm-inline">Kembali ke</span> Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home"></i>
                    <span class="d-none d-md-inline">Dashboard</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-2 px-md-3 mt-3 mt-md-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-users"></i>
                            <span class="d-none d-sm-inline">Data</span> Pelanggan
                        </h4>
                    </div>
                    <div class="card-body p-2 p-md-4">
                        <!-- Statistik Cards -->
                        <div class="row mb-3 mb-md-4 g-2 g-md-3">
                            <div class="col-12 col-md-4 mb-2 mb-md-3">
                                <div class="card stats-card border-0 bg-primary text-white">
                                    <div class="card-body text-center py-2 py-md-3">
                                        <i class="fas fa-users fa-2x mb-1 mb-md-2"></i>
                                        <h4 class="mb-1"><?php echo $stats['total_pelanggan']; ?></h4>
                                        <p class="mb-0 small">Total Pelanggan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-3">
                                <div class="card stats-card border-0 bg-success text-white">
                                    <div class="card-body text-center py-2 py-md-3">
                                        <i class="fas fa-shopping-cart fa-2x mb-1 mb-md-2"></i>
                                        <h4 class="mb-1"><?php echo $stats['total_transaksi_all']; ?></h4>
                                        <p class="mb-0 small">Total Transaksi</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-3">
                                <div class="card stats-card border-0 bg-warning text-white">
                                    <div class="card-body text-center py-2 py-md-3">
                                        <i class="fas fa-coins fa-2x mb-1 mb-md-2"></i>
                                        <h4 class="mb-1">Rp <?php echo number_format($stats['rata_rata_pembelian'], 0, ',', '.'); ?></h4>
                                        <p class="mb-0 small">Rata-rata Pembelian</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="row mb-3 mb-md-4 align-items-center">
                            <div class="col-12 col-md-8 mb-2 mb-md-0">
                                <form method="GET" class="d-flex search-container">
                                    <input type="text" name="search" class="form-control form-control-sm form-control-md me-2"
                                        placeholder="Cari nama pelanggan..." value="<?php echo $search; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i>
                                        <span class="d-none d-md-inline ms-1">Cari</span>
                                    </button>
                                </form>
                            </div>
                            <div class="col-12 col-md-4 text-end">
                                <?php if (!empty($search)): ?>
                                    <a href="pelanggan.php" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i>
                                        <span class="d-none d-md-inline">Reset</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Toggle untuk tampilan tabel mobile -->
                        <div class="d-md-none mb-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleTableView()">
                                <i class="fas fa-table"></i> <span id="toggleText">Tampilan Card</span>
                            </button>
                        </div>

                        <!-- Tabel Pelanggan -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="customerTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="d-none d-md-table-cell">No</th>
                                        <th>Nama Pelanggan</th>
                                        <th class="d-none d-lg-table-cell">Total Transaksi</th>
                                        <th class="d-none d-md-table-cell">Total Pembelian</th>
                                        <th>Total Pengeluaran</th>
                                        <th class="d-none d-lg-table-cell">Pertama Beli</th>
                                        <th class="d-none d-lg-table-cell">Terakhir Beli</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            // Tentukan status pelanggan
                                            $status = '';
                                            $badge_class = '';
                                            if ($row['total_pengeluaran'] >= 500000) {
                                                $status = 'VIP';
                                                $badge_class = 'bg-warning';
                                            } elseif ($row['total_pengeluaran'] >= 200000) {
                                                $status = 'Premium';
                                                $badge_class = 'bg-success';
                                            } else {
                                                $status = 'Regular';
                                                $badge_class = 'bg-secondary';
                                            }

                                            echo "<tr>";
                                            echo "<td class='d-none d-md-table-cell' data-label='No'>" . $no++ . "</td>";
                                            echo "<td data-label='Nama'><strong>" . $row['nama_pelanggan'] . "</strong></td>";
                                            echo "<td class='d-none d-lg-table-cell' data-label='Transaksi'>" . $row['total_transaksi'] . "x</td>";
                                            echo "<td class='d-none d-md-table-cell' data-label='Pembelian'>" . $row['total_pembelian'] . " kg</td>";
                                            echo "<td data-label='Pengeluaran'><span class='text-nowrap-mobile'>Rp " . number_format($row['total_pengeluaran'], 0, ',', '.') . "</span></td>";
                                            echo "<td class='d-none d-lg-table-cell' data-label='Pertama Beli'>" . date('d/m/Y', strtotime($row['pertama_beli'])) . "</td>";
                                            echo "<td class='d-none d-lg-table-cell' data-label='Terakhir Beli'>" . date('d/m/Y', strtotime($row['terakhir_beli'])) . "</td>";
                                            echo "<td data-label='Status'><span class='badge $badge_class customer-badge'>$status</span></td>";
                                            echo "<td data-label='Aksi'><button class='btn btn-sm btn-info' onclick='lihatDetail(\"" . $row['nama_pelanggan'] . "\")'>";
                                            echo "<i class='fas fa-eye'></i> <span class='d-none d-md-inline'>Detail</span></button></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data pelanggan</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pelanggan -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function lihatDetail(namaPelanggan) {
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            // Reset content dengan loading spinner
            document.getElementById('detailContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch('detail_pelanggan.php?nama=' + encodeURIComponent(namaPelanggan))
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = data;
                })
                .catch(() => {
                    document.getElementById('detailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data</div>';
                });
        }

        // Toggle table view for mobile
        let isMobileStackView = false;

        function toggleTableView() {
            const table = document.getElementById('customerTable');
            const toggleText = document.getElementById('toggleText');

            if (isMobileStackView) {
                table.classList.remove('table-mobile-stack');
                toggleText.textContent = 'Tampilan Card';
                isMobileStackView = false;
            } else {
                table.classList.add('table-mobile-stack');
                toggleText.textContent = 'Tampilan Tabel';
                isMobileStackView = true;
            }
        }

        // Auto-adjust table for very small screens
        function checkScreenSize() {
            if (window.innerWidth <= 576) {
                document.getElementById('customerTable').classList.add('table-mobile-stack');
                document.getElementById('toggleText').textContent = 'Tampilan Tabel';
                isMobileStackView = true;
            } else if (window.innerWidth > 768) {
                document.getElementById('customerTable').classList.remove('table-mobile-stack');
                document.getElementById('toggleText').textContent = 'Tampilan Card';
                isMobileStackView = false;
            }
        }

        // Check screen size on load and resize
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);
    </script>
</body>

</html>

<?php $conn->close(); ?>