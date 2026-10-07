<?php

// Memulai konfigurasi pelaporan kesalahan PHP untuk mendeteksi potensi error saat pengembangan
error_reporting(E_ALL);

// Menampilkan pesan kesalahan di layar jika terjadi masalah koneksi
ini_set('display_errors', 1);

// Menentukan alamat host server database MySQL (menggunakan localhost atau IP 127.0.0.1)
$db_host = '127.0.0.1';

// Menentukan username akses database MySQL default untuk XAMPP
$db_user = 'root';

// Menentukan kata sandi database MySQL default (kosong untuk instalasi standar XAMPP)
$db_pass = '';

// Menentukan nama database sesuai dengan ketentuan yang ada di dalam modul soal
$db_name = 'db_laundry_express';

// Menyimpan daftar port MySQL yang umum digunakan (3307 untuk lingkungan lokal saat ini dan 3306 untuk XAMPP standar)
$ports_to_try = [3307, 3306];

// Menginisialisasi variabel koneksi awal dengan nilai null
$koneksi = null;

// Menginisialisasi variabel untuk menampung pesan kesalahan jika semua port gagal dihubungi
$last_error = '';

// Melakukan perulangan untuk mencoba menyambungkan koneksi ke masing-masing port yang telah ditentukan
foreach ($ports_to_try as $port) {
    // Mencoba melakukan koneksi ke MySQL menggunakan fungsi mysqli_connect dengan penanganan silent (@)
    $test_conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $port);

    // Memeriksa apakah proses koneksi ke port tersebut berhasil dilakukan
    if ($test_conn) {
        // Jika berhasil, simpan koneksi yang valid ke dalam variabel utama $koneksi
        $koneksi = $test_conn;
        // Menghentikan perulangan karena koneksi database aktif sudah ditemukan
        break;
    } else {
        // Jika gagal pada port ini, rekam pesan error terakhir untuk informasi pelaporan jika dibutuhkan
        $last_error = mysqli_connect_error();
    }
}

// Memeriksa apakah variabel $koneksi masih bernilai null setelah mencoba semua port
if (! $koneksi) {
    // Jika tidak ada port yang berhasil tersambung, hentikan eksekusi script dan tampilkan pesan error yang jelas
    exit('Koneksi Database Gagal! Pastikan MySQL pada XAMPP sudah dinyalakan. Pesan Error: '.$last_error);
}

// Mengatur kumpulan karakter (charset) koneksi ke utf8mb4 agar mendukung karakter khusus dan simbol
mysqli_set_charset($koneksi, 'utf8mb4');

// Membuat alias variabel $conn agar kompatibel dengan penulisan $conn maupun $koneksi di berbagai modul
$conn = $koneksi;

// Memeriksa apakah session PHP belum dimulai pada halaman yang memanggil koneksi ini
if (session_status() === PHP_SESSION_NONE) {
    // Memulai sesi PHP agar data login admin dan notifikasi dapat disimpan di seluruh halaman
    session_start();
}

// Mendefinisikan fungsi pembantu untuk memformat angka nominal uang menjadi format Rupiah Indonesia
function format_rupiah($angka)
{
    // Mengubah angka numerik menjadi string mata uang berformat 'Rp xx.xxx' tanpa desimal
    return 'Rp '.number_format((int) $angka, 0, ',', '.');
}

// Mendefinisikan fungsi pembantu untuk membersihkan input data dari form agar aman dari ancaman XSS
function bersihkan_input($koneksi, $data)
{
    // Menghapus spasi yang tidak perlu di awal dan akhir string
    $data = trim($data);
    // Menghapus karakter backslash dari data
    $data = stripslashes($data);
    // Mengubah karakter khusus HTML menjadi entitas teks biasa untuk mencegah eksekusi kode jahat (XSS)
    $data = htmlspecialchars($data);

    // Menghindari SQL Injection dengan fungsi pelindung bawaan mysqli
    return mysqli_real_escape_string($koneksi, $data);
}
