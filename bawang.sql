--DATABASE Mysql

CREATE DATABASE bawang_db;

USE bawang_db;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

INSERT INTO admin (username, password) VALUES ('admin', MD5('admin123'));

CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE,
    pembeli VARCHAR(100),
    jumlah INT,
    harga INT
);

CREATE TABLE stok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jumlah_stok INT,
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO stok (jumlah_stok) VALUES (100);

CREATE TABLE IF NOT EXISTS riwayat_stok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE,
    jenis_transaksi ENUM('masuk', 'keluar'),
    jumlah INT,
    keterangan TEXT,
    stok_sebelum INT,
    stok_sesudah INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);