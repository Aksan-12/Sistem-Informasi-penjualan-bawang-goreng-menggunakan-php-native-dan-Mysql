<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #f0f2f5, #e2e6ea);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .form-control::placeholder {
            font-style: italic;
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
    <div class="login-card">
        <h3 class="text-center mb-4">Login Admin</h3>
        <form action="proses_login.php" method="post">
            <div class="form-group mb-3">
                <i class="bi bi-person-fill form-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group mb-3">
                <i class="bi bi-lock-fill form-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none">Belum punya akun? <strong>Daftar di sini</strong></a>
            </div>
        </form>
    </div>
</body>

</html>