<?php
session_start();
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Koneksi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bawang_db';
$port = 3307;
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nama pelanggan dari parameter
$nama_pelanggan = isset($_GET['nama']) ? $_GET['nama'] : '';

if (empty($nama_pelanggan)) {
    http_response_code(400);
    exit('Nama pelanggan tidak boleh kosong');
}

// Query untuk mendapatkan detail transaksi pelanggan
$query = "SELECT 
    tanggal,
    jumlah,
    harga,
    (jumlah * harga) as total_harga
    FROM penjualan 
    WHERE pembeli = ? 
    ORDER BY tanggal DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nama_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

// Query untuk statistik pelanggan
$query_stats = "SELECT 
    COUNT(*) as total_transaksi,
    SUM(jumlah) as total_pembelian,
    SUM(jumlah * harga) as total_pengeluaran,
    AVG(jumlah * harga) as rata_rata_transaksi,
    MAX(tanggal) as terakhir_beli,
    MIN(tanggal) as pertama_beli
    FROM penjualan 
    WHERE pembeli = ?";

$stmt_stats = $conn->prepare($query_stats);
$stmt_stats->bind_param("s", $nama_pelanggan);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Tentukan status pelanggan
$status = '';
$badge_class = '';
if ($stats['total_pengeluaran'] >= 500000) {
    $status = 'VIP';
    $badge_class = 'bg-warning';
} elseif ($stats['total_pengeluaran'] >= 200000) {
    $status = 'Premium';
    $badge_class = 'bg-success';
} else {
    $status = 'Regular';
    $badge_class = 'bg-secondary';
}
?>

<div class="container-fluid">
    <!-- Header Info Pelanggan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-1"><?php echo htmlspecialchars($nama_pelanggan); ?></h5>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Pelanggan sejak: <?php echo date('d/m/Y', strtotime($stats['pertama_beli'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Pelanggan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <h6><?php echo $stats['total_transaksi']; ?></h6>
                    <small>Total Transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center">
                    <h6><?php echo $stats['total_pembelian']; ?> kg</h6>
                    <small>Total Pembelian</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-white">
                <div class="card-body text-center">
                    <h6>Rp <?php echo number_format($stats['total_pengeluaran'], 0, ',', '.'); ?></h6>
                    <small>Total Pengeluaran</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body text-center">
                    <h6>Rp <?php echo number_format($stats['rata_rata_transaksi'], 0, ',', '.'); ?></h6>
                    <small>Rata-rata Transaksi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="row">
        <div class="col-12">
            <h6 class="mb-3">Riwayat Transaksi</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Harga/kg</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                echo "<td>" . $row['jumlah'] . " kg</td>";
                                echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
?>