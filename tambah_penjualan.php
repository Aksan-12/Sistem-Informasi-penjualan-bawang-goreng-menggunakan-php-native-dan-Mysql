<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $pembeli = $_POST['pembeli'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    $koneksi->query("INSERT INTO penjualan (tanggal, pembeli, jumlah, harga) VALUES ('$tanggal', '$pembeli', '$jumlah', '$harga')");
    header("Location: penjualan.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Tambahan agar responsive -->
    <title>Tambah Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
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

        .form-label {
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 576px) {
            .card {
                padding: 1.5rem;
            }

            h4 {
                font-size: 1.25rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="penjualan.php">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Penjualan
            </a>
        </div>
    </nav>

    <!-- Konten Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10"> <!-- Lebih fleksibel untuk berbagai ukuran layar -->
                <div class="card p-4">
                    <h4 class="mb-4 text-center"><i class="fas fa-plus-circle"></i> Tambah Data Penjualan</h4>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Pembeli</label>
                            <input type="text" name="pembeli" class="form-control" placeholder="Contoh: Budi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah (kg)</label>
                            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Total</label>
                            <input type="number" name="harga" class="form-control" placeholder="Contoh: 50000" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="penjualan.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>