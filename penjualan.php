<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');
include "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar-dark {
            background-color: #1e1e1e;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #1e1e1e;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 12px;
        }

        .table tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
            color: #333;
        }

        .table tfoot td {
            background-color: #fff3cd;
            font-weight: bold;
            font-size: 1rem;
            text-align: right;
            padding: 12px;
            color: #000;
        }

        .table tfoot td:first-child {
            text-align: left;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }

        .btn-warning {
            background-color: #f39c12;
            color: #fff;
            border: none;
        }

        .btn-danger:hover,
        .btn-warning:hover {
            opacity: 0.9;
        }

        .page-title {
            font-weight: bold;
        }

        @media (max-width: 768px) {

            .table th,
            .table td {
                font-size: 0.85rem;
                padding: 6px;
            }

            .btn-sm {
                padding: 4px 8px;
                font-size: 0.75rem;
            }

            .navbar-brand,
            .btn-outline-light {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="card p-4">
            <h4 class="page-title text-center mb-4"><i class="fas fa-chart-line"></i> Data Penjualan Bawang Goreng</h4>

            <!-- Tombol Tambah -->
            <div class="mb-3 text-end">
                <a href="tambah_penjualan.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </a>
            </div>

            <!-- Tabel Penjualan -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Jumlah (kg)</th>
                            <th>Harga per kg</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $grand_total = 0;
                        $query = $koneksi->query("SELECT * FROM penjualan ORDER BY tanggal DESC");
                        while ($row = $query->fetch_assoc()) {
                            $total = $row['jumlah'] * $row['harga'];
                            $grand_total += $total;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['pembeli']) ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                                <td>
                                    <a href="edit_penjualan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"><strong>GRAND TOTAL</strong></td>
                            <td colspan="2"><strong>Rp <?= number_format($grand_total, 0, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Hapus -->
    <script>
        document.querySelectorAll('.btn-hapus').forEach(function(button) {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'hapus_penjualan.php?id=' + id;
                    }
                })
            });
        });
    </script>

</body>

</html>