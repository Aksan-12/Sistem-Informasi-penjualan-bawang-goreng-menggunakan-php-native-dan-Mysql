<?php
$koneksi = new mysqli(
    "localhost",
    "root",
    "",
    "bawang_db",
    3307
);
if ($koneksi->connect_error) {
    die("Koneksi Gagal: " . $koneksi->connect_error);
}
