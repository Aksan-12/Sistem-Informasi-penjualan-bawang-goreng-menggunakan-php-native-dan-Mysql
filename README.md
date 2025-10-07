# Sistem Informasi Penjualan Bawang Goreng

Sistem informasi sederhana untuk manajemen penjualan bawang goreng yang dibuat menggunakan PHP native dan MySQL. Aplikasi ini mencakup fitur-fitur dasar untuk pencatatan penjualan, manajemen stok, pengelolaan data pelanggan, dan pelaporan.

## Tentang Proyek

Proyek ini bertujuan untuk menyediakan solusi digital bagi usaha kecil menengah (UKM) yang bergerak di bidang penjualan bawang goreng. Dengan antarmuka yang ramah pengguna dan responsif, aplikasi ini memudahkan pemilik usaha untuk memantau aktivitas bisnis mereka secara efisien, baik melalui perangkat desktop maupun mobile.

## Fitur

* **Dashboard Interaktif**: Menampilkan ringkasan statistik penjualan harian, pendapatan bulanan, jumlah transaksi, dan sisa stok.
* **Manajemen Penjualan (CRUD)**:
    * Mencatat data penjualan baru (Tanggal, Pembeli, Jumlah, Harga).
    * Mengubah dan menghapus data penjualan yang sudah ada.
* **Manajemen Stok**:
    * Fitur untuk menambah (stok masuk) dan mengurangi (stok keluar).
    * Menampilkan riwayat perubahan stok.
    * Peringatan otomatis jika stok menipis.
* **Pengelolaan Pelanggan**:
    * Melihat daftar pelanggan beserta total transaksi dan total pembelian.
    * Fitur pencarian pelanggan.
    * Melihat detail riwayat transaksi per pelanggan.
* **Laporan Penjualan**:
    * Menampilkan laporan penjualan dalam rentang tanggal tertentu.
    * Statistik ringkas seperti total transaksi, total pendapatan, dan rata-rata transaksi.
    * Fitur ekspor laporan ke format **CSV** dan **PDF**.
* **Autentikasi**: Sistem login dan registrasi untuk admin.
* **Desain Responsif**: Tampilan yang dapat menyesuaikan dengan berbagai ukuran layar, dari desktop hingga ponsel.

## Teknologi yang Digunakan

* **Backend**: PHP Native (procedural)
* **Database**: MySQL
* **Frontend**:
    * Bootstrap 5
    * Font Awesome
    * SweetAlert2
* **Library PDF**: jsPDF & jsPDF-AutoTable

## Prasyarat

* Web Server (Contoh: XAMPP, Laragon)
* PHP versi 8.0 atau lebih tinggi
* MySQL

## Instalasi

1.  **Clone Repositori**
    ```sh
    git clone [https://github.com/aksan-12/sistem-informasi-penjualan-bawang-goreng-menggunakan-php-native-dan-mysql.git](https://github.com/aksan-12/sistem-informasi-penjualan-bawang-goreng-menggunakan-php-native-dan-mysql.git)
    ```

2.  **Setup Database**
    * Buat database baru di MySQL dengan nama `bawang_db`.
    * Impor file `bawang.sql` yang ada di dalam direktori proyek ke dalam database `bawang_db` Anda.

3.  **Konfigurasi Koneksi**
    * Buka file `koneksi.php`.
    * Sesuaikan konfigurasi berikut dengan pengaturan database Anda:
        ```php
        <?php
        $koneksi = new mysqli(
            "localhost", // host
            "root",      // username
            "",          // password
            "bawang_db", // nama database
            3307         // port (sesuaikan jika berbeda)
        );
        if ($koneksi->connect_error) {
            die("Koneksi Gagal: " . $koneksi->connect_error);
        }
        ?>
        ```

4.  **Jalankan Aplikasi**
    * Tempatkan folder proyek di dalam direktori `htdocs` (untuk XAMPP) atau `www` (untuk Laragon).
    * Jalankan web server Anda.
    * Buka browser dan akses `http://localhost/[nama-folder-proyek]`.

5.  **Login Admin**
    * Gunakan akun default berikut untuk login:
        * **Username**: `admin`
        * **Password**: `admin123`
    * Anda juga dapat mendaftarkan admin baru melalui halaman registrasi.

## Struktur Database

Database `bawang_db` memiliki beberapa tabel utama:

* `admin`: Menyimpan data login admin.
* `penjualan`: Mencatat semua transaksi penjualan.
* `stok`: Menyimpan informasi jumlah stok bawang goreng saat ini.
* `riwayat_stok`: (Opsional, jika diimplementasikan) Mencatat riwayat masuk dan keluar stok.
