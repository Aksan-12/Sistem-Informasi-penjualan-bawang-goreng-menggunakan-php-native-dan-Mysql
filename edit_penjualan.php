<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');
include "koneksi.php";

$id = $_GET['id'];
$data = $koneksi->query("SELECT * FROM penjualan WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $pembeli = $_POST['pembeli'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];

    $koneksi->query("UPDATE penjualan SET tanggal='$tanggal', pembeli='$pembeli', jumlah='$jumlah', harga='$harga' WHERE id=$id");
    header("Location: penjualan.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-custom {
            max-width: 700px;
            margin: 40px auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .card-header {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 10px;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            border-radius: 10px;
        }

        .btn i {
            margin-right: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card card-custom">
            <div class="card-header py-3">
                <h4><i class="fas fa-edit"></i> Edit Data Penjualan</h4>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="<?= $data['tanggal'] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pembeli" class="form-label">Nama Pembeli</label>
                        <input type="text" name="pembeli" id="pembeli" value="<?= $data['pembeli'] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (kg)</label>
                        <input type="number" name="jumlah" id="jumlah" value="<?= $data['jumlah'] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Total</label>
                        <input type="number" name="harga" id="harga" value="<?= $data['harga'] ?>" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="penjualan.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>