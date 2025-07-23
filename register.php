<?php
include "koneksi.php";

$status = ""; // status pesan: success / exists

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Cek apakah username sudah ada
    $cek = $koneksi->query("SELECT * FROM admin WHERE username='$username'");
    if ($cek->num_rows > 0) {
        $status = "exists";
    } else {
        $koneksi->query("INSERT INTO admin (username, password) VALUES ('$username', '$password')");
        $status = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(to right, #f0f2f5, #e2e6ea);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-card {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .form-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .form-group {
            position: relative;
        }

        .form-control {
            padding-left: 35px;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <h3 class="text-center mb-4">Register Admin</h3>
        <form action="" method="post">
            <div class="form-group mb-3">
                <i class="bi bi-person-plus-fill form-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group mb-3">
                <i class="bi bi-lock-fill form-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button class="btn btn-success w-100">Daftar</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Kembali ke Login</a>
        </form>
    </div>

    <script>
        <?php if ($status == "success") : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Akun berhasil didaftarkan.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        <?php elseif ($status == "exists") : ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Username sudah digunakan.',
                confirmButtonText: 'Coba Lagi'
            });
        <?php endif; ?>
    </script>
</body>

</html>