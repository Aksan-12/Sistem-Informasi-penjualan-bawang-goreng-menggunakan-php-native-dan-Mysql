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

// Query untuk statistik
$query_hari_ini = "SELECT COALESCE(SUM(jumlah), 0) as total FROM penjualan WHERE tanggal = CURDATE()";
$result_hari_ini = $conn->query($query_hari_ini);
$total_penjualan_hari_ini = $result_hari_ini->fetch_assoc()['total'];

$query_bulan_ini = "SELECT COALESCE(SUM(jumlah * harga), 0) as total FROM penjualan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
$result_bulan_ini = $conn->query($query_bulan_ini);
$total_pendapatan_bulan_ini = $result_bulan_ini->fetch_assoc()['total'];

$query_transaksi = "SELECT COUNT(*) as total FROM penjualan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())";
$result_transaksi = $conn->query($query_transaksi);
$jumlah_transaksi = $result_transaksi->fetch_assoc()['total'];

$stok_tersisa = 50;
$nama_pengguna = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Penjualan Bawang Goreng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .dashboard-img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .stats-card {
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .welcome-section {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .quick-action-btn {
            margin: 5px 0;
            min-width: 150px;
        }

        .alert-custom {
            border-left: 4px solid #dc3545;
        }

        @media (max-width: 768px) {
            .welcome-section {
                text-align: center;
            }

            .welcome-section .row>div {
                margin-bottom: 15px;
            }

            .dashboard-img {
                max-height: 200px;
            }

            .quick-action-btn {
                min-width: 100%;
            }

            .navbar-text {
                font-size: 14px;
            }

            .navbar .btn {
                padding: 5px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-store"></i> Toko Bawang Goreng</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user"></i> <?php echo $nama_pengguna; ?>
                </span>
                <button id="logoutBtn" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 px-3">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Penjualan Bawang Goreng</h2>
                    <p class="mb-0">Selamat datang kembali, <?php echo $nama_pengguna; ?>! Kelola bisnis bawang goreng Anda dengan mudah.</p>
                    <small class="text-light">Terakhir login: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-seedling fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h4 class="card-title text-primary"><?php echo $total_penjualan_hari_ini; ?></h4>
                        <p class="card-text text-muted">Penjualan Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="fas fa-rupiah-sign fa-2x"></i>
                        </div>
                        <h4 class="card-title text-success">Rp <?php echo number_format($total_pendapatan_bulan_ini, 0, ',', '.'); ?></h4>
                        <p class="card-text text-muted">Pendapatan Bulan Ini</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h4 class="card-title text-info"><?php echo $jumlah_transaksi; ?></h4>
                        <p class="card-text text-muted">Total Transaksi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                        <h4 class="card-title text-warning"><?php echo $stok_tersisa; ?> kg</h4>
                        <p class="card-text text-muted">Stok Tersisa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Section -->
        <?php if ($stok_tersisa < 20): ?>
            <div class="alert alert-warning alert-custom d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian!</strong> Stok bawang goreng tinggal <?php echo $stok_tersisa; ?> kg. Segera lakukan restok.
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Main Image -->
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-image"></i> Produk Unggulan</h5>
                        <img src="img/bawang.jpg" alt="Bawang Goreng" class="dashboard-img">
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
                        <div class="d-grid gap-2">
                            <a href="penjualan.php" class="btn btn-warning quick-action-btn w-100">
                                <i class="fas fa-plus"></i> Tambah Penjualan
                            </a>
                            <a href="kelola_stok.php" class="btn btn-info quick-action-btn w-100">
                                <i class="fas fa-boxes"></i> Kelola Stok
                            </a>
                            <a href="laporan.php" class="btn btn-success quick-action-btn w-100">
                                <i class="fas fa-chart-bar"></i> Lihat Laporan
                            </a>
                            <a href="pelanggan.php" class="btn btn-primary quick-action-btn w-100">
                                <i class="fas fa-users"></i> Data Pelanggan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clock"></i> Penjualan Terakhir</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pembeli</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_recent = "SELECT * FROM penjualan ORDER BY id DESC LIMIT 5";
                                    $result_recent = $conn->query($query_recent);

                                    if ($result_recent->num_rows > 0) {
                                        while ($row = $result_recent->fetch_assoc()) {
                                            $total = $row['jumlah'] * $row['harga'];
                                            echo "<tr>";
                                            echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                            echo "<td>" . $row['pembeli'] . "</td>";
                                            echo "<td>" . $row['jumlah'] . " kg</td>";
                                            echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                            echo "<td>Rp " . number_format($total, 0, ',', '.') . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Belum ada data penjualan</td></tr>";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Sedang memproses logout',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        window.location.href = 'proses_logout.php';
                    }, 1000);
                }
            });
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>