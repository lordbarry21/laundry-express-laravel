<?php

// Memulai atau menyambungkan kembali sesi PHP yang sedang aktif
session_start();

// Mengosongkan semua variabel array data yang tersimpan di dalam $_SESSION
$_SESSION = [];

// Memeriksa apakah cookie sesi ada di peramban, jika ada maka hapus cookie tersebut
if (ini_get('session.use_cookies')) {
    // Mengambil konfigurasi parameter cookie sesi yang sedang berlaku
    $params = session_get_cookie_params();
    // Mengatur masa kedaluwarsa cookie sesi ke waktu lampau agar peramban menghapusnya
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Menghancurkan seluruh data sesi di sisi server
session_destroy();

// Mengalihkan pengguna kembali ke halaman form login dengan parameter notifikasi logout
header('Location: login.php?pesan=logout');

// Menghentikan eksekusi script setelah pengalihan
exit;
