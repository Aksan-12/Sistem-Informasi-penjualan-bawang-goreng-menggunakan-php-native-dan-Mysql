<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = $koneksi->query("SELECT * FROM admin WHERE username='$username' AND password='$password'");
if ($sql->num_rows > 0) {
    $_SESSION['login'] = true;
    header("Location: dashboard.php");
} else {
    echo "<script>alert('Login Gagal'); window.location='index.php';</script>";
}
