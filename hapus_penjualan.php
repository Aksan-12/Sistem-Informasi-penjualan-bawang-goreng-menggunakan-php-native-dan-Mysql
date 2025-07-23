<?php
session_start();
if (!isset($_SESSION['login'])) header('Location: index.php');
include "koneksi.php";

$id = $_GET['id'];
$koneksi->query("DELETE FROM penjualan WHERE id=$id");

header("Location: penjualan.php");
