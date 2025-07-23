<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bawang_db';
$port = 3307;
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');

// Query laporan
$query = "SELECT * FROM penjualan WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC";
$result = $conn->query($query);

// Statistik
$query_stats = "SELECT 
    COUNT(*) as total_transaksi,
    SUM(jumlah) as total_jumlah,
    SUM(jumlah * harga) as total_pendapatan,
    AVG(jumlah * harga) as rata_rata_transaksi
    FROM penjualan 
    WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
$result_stats = $conn->query($query_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- jsPDF dan autotable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <style>
        .stats-card {
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            h4 {
                font-size: 1.25rem;
            }

            /* Stats cards responsive */
            .stats-card .card-body {
                padding: 1rem 0.75rem;
            }

            .stats-card h4 {
                font-size: 1.25rem;
                margin-bottom: 0.25rem;
            }

            .stats-card p {
                font-size: 0.85rem;
                margin-bottom: 0;
            }

            .stats-card .fa-2x {
                font-size: 1.5em !important;
            }

            /* Form responsive */
            .filter-form .col-md-4 {
                margin-bottom: 1rem;
            }

            .filter-form label {
                font-size: 0.9rem;
                margin-bottom: 0.25rem;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            /* Export buttons */
            .export-buttons {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .export-buttons .btn {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
            }

            /* Table responsive */
            .table-responsive {
                border: none;
                font-size: 0.8rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                white-space: nowrap;
            }

            .table th {
                font-size: 0.75rem;
            }

            /* Hide some columns on mobile */
            .hide-mobile {
                display: none;
            }

            /* Alert responsive */
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            /* Card responsive */
            .card-header h4 {
                font-size: 1.1rem;
            }

            /* Navbar responsive */
            .navbar-brand {
                font-size: 0.9rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
        }

        @media (max-width: 576px) {

            /* Extra small screens */
            .stats-card .card-body {
                text-align: center;
                padding: 0.75rem 0.5rem;
            }

            .stats-card h4 {
                font-size: 1rem;
            }

            .stats-card p {
                font-size: 0.8rem;
            }

            .table {
                font-size: 0.7rem;
            }

            .table th,
            .table td {
                padding: 0.25rem 0.1rem;
            }

            /* Stack filter form vertically */
            .filter-form .row {
                flex-direction: column;
            }

            .filter-form .col-md-4 {
                width: 100%;
                margin-bottom: 0.75rem;
            }

            /* Mobile table alternative - card view */
            .mobile-card-view {
                display: block;
            }

            .desktop-table-view {
                display: none;
            }

            .transaction-card {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                padding: 1rem;
                margin-bottom: 1rem;
                background-color: white;
            }

            .transaction-card .transaction-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #eee;
            }

            .transaction-card .transaction-details {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
                font-size: 0.85rem;
            }

            .transaction-card .detail-item {
                display: flex;
                justify-content: space-between;
            }

            .transaction-card .detail-label {
                font-weight: 600;
                color: #666;
            }
        }

        @media (min-width: 577px) {
            .mobile-card-view {
                display: none;
            }

            .desktop-table-view {
                display: block;
            }
        }

        /* Touch-friendly elements */
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

    <!-- Navbar -->
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

    <!-- Konten -->
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-chart-bar"></i>
                    <span class="d-none d-sm-inline">Laporan Penjualan Bawang Goreng</span>
                    <span class="d-inline d-sm-none">Laporan Penjualan</span>
                </h4>
            </div>
            <div class="card-body">

                <!-- Filter -->
                <form method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Tanggal Awal:</label>
                            <input type="date" name="tanggal_awal" class="form-control" value="<?= $tanggal_awal ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Tanggal Akhir:</label>
                            <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggal_akhir ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card stats-card bg-primary text-white text-center">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h4><?= $stats['total_transaksi'] ?></h4>
                                <p>
                                    <span class="d-none d-sm-inline">Total Transaksi</span>
                                    <span class="d-inline d-sm-none">Transaksi</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card bg-success text-white text-center">
                            <div class="card-body">
                                <i class="fas fa-weight fa-2x mb-2"></i>
                                <h4><?= $stats['total_jumlah'] ?> kg</h4>
                                <p>
                                    <span class="d-none d-sm-inline">Total Terjual</span>
                                    <span class="d-inline d-sm-none">Terjual</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card bg-warning text-white text-center">
                            <div class="card-body">
                                <i class="fas fa-coins fa-2x mb-2"></i>
                                <h4 class="d-none d-sm-block">Rp <?= number_format($stats['total_pendapatan'], 0, ',', '.') ?></h4>
                                <h4 class="d-block d-sm-none">Rp <?= number_format($stats['total_pendapatan'] / 1000, 0, ',', '.') ?>K</h4>
                                <p>
                                    <span class="d-none d-sm-inline">Total Pendapatan</span>
                                    <span class="d-inline d-sm-none">Pendapatan</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card bg-info text-white text-center">
                            <div class="card-body">
                                <i class="fas fa-calculator fa-2x mb-2"></i>
                                <h4 class="d-none d-sm-block">Rp <?= number_format($stats['rata_rata_transaksi'], 0, ',', '.') ?></h4>
                                <h4 class="d-block d-sm-none">Rp <?= number_format($stats['rata_rata_transaksi'] / 1000, 0, ',', '.') ?>K</h4>
                                <p>
                                    <span class="d-none d-sm-inline">Rata-rata Transaksi</span>
                                    <span class="d-inline d-sm-none">Rata-rata</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Periode -->
                <div class="alert alert-info">
                    <i class="fas fa-calendar"></i>
                    <span class="d-none d-sm-inline">Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></span>
                    <span class="d-inline d-sm-none"><?= date('d/m', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></span>
                </div>

                <!-- Tombol Export -->
                <div class="mb-3">
                    <div class="export-buttons d-md-flex gap-2">
                        <button onclick="exportToCSV()" class="btn btn-success">
                            <i class="fas fa-file-csv"></i>
                            <span class="d-none d-sm-inline">Export CSV</span>
                            <span class="d-inline d-sm-none">CSV</span>
                        </button>
                        <button onclick="exportToPDF()" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i>
                            <span class="d-none d-sm-inline">Export PDF</span>
                            <span class="d-inline d-sm-none">PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="desktop-table-view">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="laporanTable">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th class="hide-mobile">Pembeli</th>
                                    <th>Jumlah (kg)</th>
                                    <th class="d-none d-md-table-cell">Harga per kg</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    $no = 1;
                                    $grand_total = 0;
                                    // Reset result pointer for table
                                    $result->data_seek(0);
                                    while ($row = $result->fetch_assoc()) {
                                        $total = $row['jumlah'] * $row['harga'];
                                        $grand_total += $total;
                                        echo "<tr>
                                            <td>$no</td>
                                            <td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>
                                            <td class='hide-mobile'>{$row['pembeli']}</td>
                                            <td>{$row['jumlah']}</td>
                                            <td class='d-none d-md-table-cell'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                                            <td>Rp " . number_format($total, 0, ',', '.') . "</td>
                                        </tr>";
                                        $no++;
                                    }
                                    echo "<tr class='table-warning text-end'>
                                        <td colspan='5' class='d-none d-md-table-cell'><strong>GRAND TOTAL</strong></td>
                                        <td colspan='4' class='d-md-none'><strong>GRAND TOTAL</strong></td>
                                        <td><strong>Rp " . number_format($grand_total, 0, ',', '.') . "</strong></td>
                                    </tr>";
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data untuk periode ini</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="mobile-card-view">
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        $grand_total = 0;
                        // Reset result pointer for mobile cards
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()) {
                            $total = $row['jumlah'] * $row['harga'];
                            $grand_total += $total;
                            echo "<div class='transaction-card'>
                                <div class='transaction-header'>
                                    <span><strong>#{$no}</strong> - " . date('d/m/Y', strtotime($row['tanggal'])) . "</span>
                                    <span class='badge bg-primary'>Rp " . number_format($total, 0, ',', '.') . "</span>
                                </div>
                                <div class='transaction-details'>
                                    <div class='detail-item'>
                                        <span class='detail-label'>Pembeli:</span>
                                        <span>{$row['pembeli']}</span>
                                    </div>
                                    <div class='detail-item'>
                                        <span class='detail-label'>Jumlah:</span>
                                        <span>{$row['jumlah']} kg</span>
                                    </div>
                                    <div class='detail-item'>
                                        <span class='detail-label'>Harga/kg:</span>
                                        <span>Rp " . number_format($row['harga'], 0, ',', '.') . "</span>
                                    </div>
                                    <div class='detail-item'>
                                        <span class='detail-label'>Total:</span>
                                        <span><strong>Rp " . number_format($total, 0, ',', '.') . "</strong></span>
                                    </div>
                                </div>
                            </div>";
                            $no++;
                        }
                        echo "<div class='alert alert-warning text-center'>
                            <strong>GRAND TOTAL: Rp " . number_format($grand_total, 0, ',', '.') . "</strong>
                        </div>";
                    } else {
                        echo "<div class='alert alert-info text-center'>Tidak ada data untuk periode ini</div>";
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Export -->
    <script>
        function exportToCSV() {
            const table = document.getElementById('laporanTable');
            let csv = [];

            const headers = [...table.rows[0].cells].map(cell => cell.innerText);
            csv.push(headers.join(','));

            for (let i = 1; i < table.rows.length; i++) {
                const row = [...table.rows[i].cells].map(cell => `"${cell.innerText}"`);
                csv.push(row.join(','));
            }

            const blob = new Blob([csv.join('\n')], {
                type: 'text/csv'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'laporan_penjualan_<?= date("Y-m-d") ?>.csv';
            link.click();
        }

        // Ganti fungsi exportToPDF() yang ada dengan kode ini:

        async function exportToPDF() {
            try {
                // Deteksi mobile device
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();
                const title = "LAPORAN PENJUALAN BAWANG GORENG";
                const periode = "Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?>";

                const headers = [
                    ["No", "Tanggal", "Pembeli", "Jumlah (kg)", "Harga per kg", "Total"]
                ];
                const dataRows = [];

                // Ambil data dari tabel (gunakan yang desktop view untuk data lengkap)
                const rows = document.querySelectorAll('#laporanTable tbody tr');

                for (const row of rows) {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        const rowData = [];
                        cells.forEach((cell) => {
                            // Bersihkan text dari whitespace berlebih
                            rowData.push(cell.innerText.trim());
                        });
                        dataRows.push(rowData);
                    }
                }

                doc.setFontSize(14);
                doc.text(title, 105, 15, {
                    align: "center"
                });
                doc.setFontSize(10);
                doc.text(periode, 105, 22, {
                    align: "center"
                });

                doc.autoTable({
                    startY: 30,
                    head: headers,
                    body: dataRows,
                    styles: {
                        halign: 'center',
                        valign: 'middle',
                        fontSize: 8 // Kecilkan font untuk mobile
                    },
                    headStyles: {
                        fillColor: [40, 40, 40],
                        textColor: 255
                    },
                    theme: 'grid'
                });

                const filename = "laporan_penjualan_<?= date('Y-m-d') ?>.pdf";

                if (isMobile) {
                    // Untuk mobile - gunakan metode yang lebih kompatibel
                    const pdfBlob = doc.output('blob');

                    // Coba metode 1: Direct download
                    try {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(pdfBlob);
                        link.download = filename;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Cleanup
                        setTimeout(() => {
                            URL.revokeObjectURL(link.href);
                        }, 1000);

                    } catch (downloadError) {
                        console.error('Direct download failed:', downloadError);

                        // Metode 2: Buka di tab baru
                        const pdfDataUri = doc.output('datauristring');
                        const newWindow = window.open();
                        newWindow.document.write(`
                    <html>
                        <head>
                            <title>Laporan PDF</title>
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        </head>
                        <body style="margin:0;padding:20px;text-align:center;">
                            <h3>Laporan PDF Siap Diunduh</h3>
                            <iframe src="${pdfDataUri}" width="100%" height="600px" style="border:none;"></iframe>
                            <br><br>
                            <a href="${pdfDataUri}" download="${filename}" 
                               style="display:inline-block;padding:10px 20px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;">
                               Download PDF
                            </a>
                        </body>
                    </html>
                `);
                    }
                } else {
                    // Untuk desktop - gunakan metode normal
                    doc.save(filename);
                }

            } catch (error) {
                console.error('PDF Export Error:', error);
                alert('Terjadi kesalahan saat membuat PDF. Silakan coba lagi.');
            }
        }

        // Fungsi alternatif jika masih bermasalah
        function exportToPDFAlternative() {
            try {
                // Buat tabel sederhana untuk mobile
                const stats = {
                    total_transaksi: '<?= $stats["total_transaksi"] ?>',
                    total_jumlah: '<?= $stats["total_jumlah"] ?>',
                    total_pendapatan: '<?= number_format($stats["total_pendapatan"], 0, ",", ".") ?>',
                    periode: '<?= date("d/m/Y", strtotime($tanggal_awal)) ?> - <?= date("d/m/Y", strtotime($tanggal_akhir)) ?>'
                };

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();

                // Header
                doc.setFontSize(16);
                doc.text('LAPORAN PENJUALAN', 105, 20, {
                    align: 'center'
                });
                doc.setFontSize(12);
                doc.text('BAWANG GORENG', 105, 28, {
                    align: 'center'
                });
                doc.setFontSize(10);
                doc.text(`Periode: ${stats.periode}`, 105, 36, {
                    align: 'center'
                });

                // Statistik
                doc.setFontSize(12);
                doc.text('RINGKASAN:', 20, 50);
                doc.setFontSize(10);
                doc.text(`Total Transaksi: ${stats.total_transaksi}`, 20, 60);
                doc.text(`Total Terjual: ${stats.total_jumlah} kg`, 20, 68);
                doc.text(`Total Pendapatan: Rp ${stats.total_pendapatan}`, 20, 76);

                // Data transaksi
                const tableData = [];
                const rows = document.querySelectorAll('#laporanTable tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 6) {
                        tableData.push([
                            cells[0].innerText.trim(),
                            cells[1].innerText.trim(),
                            cells[2].innerText.trim(),
                            cells[3].innerText.trim(),
                            cells[4].innerText.trim(),
                            cells[5].innerText.trim()
                        ]);
                    }
                });

                if (tableData.length > 0) {
                    doc.autoTable({
                        startY: 85,
                        head: [
                            ['No', 'Tanggal', 'Pembeli', 'Jumlah', 'Harga/kg', 'Total']
                        ],
                        body: tableData,
                        styles: {
                            fontSize: 8
                        },
                        headStyles: {
                            fillColor: [41, 128, 185]
                        }
                    });
                }

                // Force download dengan blob
                const pdfBlob = doc.output('blob');
                const url = URL.createObjectURL(pdfBlob);

                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `laporan_penjualan_${new Date().toISOString().slice(0,10)}.pdf`;
                document.body.appendChild(a);
                a.click();

                // Cleanup
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 1000);

            } catch (error) {
                console.error('Alternative PDF Export Error:', error);

                // Fallback: tampilkan di window baru
                const newWindow = window.open('', '_blank');
                newWindow.document.write(`
            <html>
                <head><title>Laporan Penjualan</title></head>
                <body>
                    <h2>Maaf, download PDF tidak didukung di perangkat ini</h2>
                    <p>Silakan gunakan desktop atau laptop untuk mengunduh laporan PDF.</p>
                    <button onclick="window.close()">Tutup</button>
                </body>
            </html>
        `);
            }
        }
    </script>

</body>

</html>

<?php $conn->close(); ?>