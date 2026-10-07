<?php
// Memasukkan file koneksi.php agar sistem dapat terhubung ke database dan session_start() aktif
require_once 'koneksi.php';

// Memeriksa jika admin sudah dalam keadaan login sebelumnya
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Jika sudah login, langsung alihkan ke halaman dashboard admin
    header('Location: admin/index.php');
    // Hentikan script
    exit;
}

// Menyiapkan variabel pesan error jika proses login gagal
$pesan_error = '';

// Memeriksa apakah form login dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil dan membersihkan input username dari form
    $username = bersihkan_input($koneksi, $_POST['username']);
    // Mengambil input password dari form
    $password = $_POST['password'];

    // Validasi apakah input form kosong
    if (empty($username) || empty($password)) {
        // Menyimpan pesan jika username atau password kosong
        $pesan_error = 'Silakan masukkan username dan password Anda!';
    } else {
        // Menghitung hash SHA-256 dari input password untuk dicocokkan dengan database
        $password_hash = hash('sha256', $password);

        // Menyiapkan query untuk mencari akun admin berdasarkan username
        $query = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
        // Menjalankan query ke database
        $result = mysqli_query($koneksi, $query);

        // Memeriksa apakah akun admin dengan username tersebut ditemukan
        if ($result && mysqli_num_rows($result) > 0) {
            // Mengambil baris data admin
            $admin = mysqli_fetch_assoc($result);

            // Memeriksa kecocokan kata sandi (mendukung hash SHA-256 maupun verifikasi password_hash)
            if ($admin['password'] === $password_hash || password_verify($password, $admin['password']) || $admin['password'] === $password) {
                // Menyimpan status login admin ke dalam sesi browser
                $_SESSION['admin_logged_in'] = true;
                // Menyimpan ID admin ke sesi
                $_SESSION['admin_id'] = $admin['id'];
                // Menyimpan username admin ke sesi
                $_SESSION['admin_username'] = $admin['username'];
                // Menyimpan nama lengkap admin ke sesi
                $_SESSION['admin_fullname'] = $admin['fullname'];

                // Mengalihkan halaman ke dashboard admin setelah berhasil login
                header('Location: admin/index.php');
                // Menghentikan eksekusi script
                exit;
            } else {
                // Menampilkan pesan kesalahan jika password tidak cocok
                $pesan_error = 'Password yang Anda masukkan salah!';
            }
        } else {
            // Menampilkan pesan kesalahan jika username tidak terdaftar
            $pesan_error = 'Username admin tidak ditemukan dalam sistem!';
        }
    }
}
?>
<!DOCTYPE html>
<!-- Menentukan dokumen HTML5 -->
<html lang="id">
<!-- Membuka tag head -->
<head>
    <!-- Menentukan pengkodean UTF-8 -->
    <meta charset="UTF-8">
    <!-- Skala viewport untuk layar responsif -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul tab halaman login admin -->
    <title>Login Administrator - Laundry Express</title>
    
    <!-- Memuat stylesheet Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat ikon Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Memuat Google Fonts Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Kustomisasi CSS halaman login -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
    </style>
<!-- Menutup tag head -->
</head>
<!-- Membuka tag body -->
<body>

    <!-- Kartu Login Administrator -->
    <div class="login-card">
        <!-- Bagian Kepala Form Login -->
        <div class="login-header">
            <div class="mb-3">
                <i class="fa-solid fa-user-shield fa-3x"></i>
            </div>
            <h4 class="fw-bold mb-1">Panel Administrator</h4>
            <p class="mb-0 small opacity-75">Sistem Manajemen Layanan Laundry Express</p>
        </div>

        <!-- Bagian Form Isian Login -->
        <div class="p-4 p-md-5">
            <!-- Menampilkan pesan error jika login gagal -->
            <?php if (! empty($pesan_error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $pesan_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>

            <!-- Menampilkan pesan jika baru saja logout atau dipaksa login -->
            <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'logout') { ?>
            <div class="alert alert-info rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-info-circle me-1"></i> Anda telah berhasil keluar (logout).
            </div>
            <?php } elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'belum_login') { ?>
            <div class="alert alert-warning rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-lock me-1"></i> Silakan login terlebih dahulu untuk mengakses panel admin.
            </div>
            <?php } ?>

            <!-- Form input kredensial admin -->
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="admin" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" value="admin123" required>
                    </div>
                    <div class="form-text small text-muted">Akun demo default: <strong>admin</strong> / <strong>admin123</strong></div>
                </div>

                <!-- Tombol Submit Login -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                    </button>
                </div>

                <!-- Tautan Kembali ke Beranda -->
                <div class="text-center">
                    <a href="index.php" class="text-decoration-none small text-muted">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda Pelanggan
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Memuat Javascript Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Menutup tag body dan html -->
</body>
</html>
