<?php

// Memasukkan file koneksi database dari direktori induk (satu tingkat di atas folder admin)
require_once __DIR__.'/../koneksi.php';

// Memeriksa apakah session belum aktif, jika belum maka jalankan session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah status sesi login admin bernilai benar (true)
if (! isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika admin belum login, alihkan secara paksa ke halaman login dengan pesan peringatan
    header('Location: ../login.php?pesan=belum_login');
    // Menghentikan script agar halaman admin tidak dapat diakses sama sekali
    exit;
}
