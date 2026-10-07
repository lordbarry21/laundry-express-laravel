<?php
// Memasukkan middleware verifikasi login untuk memastikan hanya admin yang berhak masuk
require_once __DIR__.'/cek_login.php';

// Menentukan nama halaman aktif saat ini untuk menandai status menu navigasi aktif
$halaman_aktif = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<!-- Menentukan tipe dokumen HTML5 -->
<html lang="id">
<!-- Membuka tag head -->
<head>
    <!-- Pengaturan karakter UTF-8 -->
    <meta charset="UTF-8">
    <!-- Viewport responsif -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul tab halaman panel admin -->
    <title><?= isset($judul_halaman) ? $judul_halaman : 'Panel Administrator'; ?> - Laundry Express</title>
    
    <!-- Memuat stylesheet Bootstrap 5 dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat ikon Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Memuat Google Fonts Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Kustomisasi CSS tata letak panel admin -->
    <style>
        :root {
            --primary-admin: #0d6efd;
            --sidebar-bg: #1e293b;
            --content-bg: #f8fafc;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--content-bg);
            color: #334155;
            min-height: 100vh;
        }
        .admin-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .nav-link.active-menu {
            color: var(--primary-admin) !important;
            font-weight: 700;
            border-bottom: 2px solid var(--primary-admin);
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .table-custom {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 700;
        }
    </style>
<!-- Menutup tag head -->
</head>
<!-- Membuka tag body -->
<body>

    <!-- Navigasi Bar Utama Admin -->
    <nav class="navbar navbar-expand-lg navbar-light admin-navbar sticky-top py-3">
        <div class="container">
            <!-- Brand Logo Admin -->
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="index.php">
                <i class="fa-solid fa-soap text-primary"></i>
                <span>Admin<span class="text-dark">Laundry</span></span>
            </a>
            <!-- Tombol Toggle Menu Responsif Mobile -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Daftar Menu Navigasi Admin -->
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <!-- Menu 1: Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= ($halaman_aktif === 'index.php') ? 'active-menu' : ''; ?>" href="index.php">
                            <i class="fa-solid fa-chart-pie me-1"></i> Dashboard
                        </a>
                    </li>
                    <!-- Menu 2: Kelola Master Layanan (CRUD Tarif) Sesuai Modul -->
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= ($halaman_aktif === 'layanan.php') ? 'active-menu' : ''; ?>" href="layanan.php">
                            <i class="fa-solid fa-tags me-1"></i> Kelola Tarif & Layanan
                        </a>
                    </li>
                    <!-- Menu 3: Kelola Transaksi Pesanan & Progres Status Sesuai Modul -->
                    <li class="nav-item">
                        <a class="nav-link px-3 <?= ($halaman_aktif === 'pesanan.php') ? 'active-menu' : ''; ?>" href="pesanan.php">
                            <i class="fa-solid fa-boxes-stacked me-1"></i> Transaksi & Progres Cuci
                        </a>
                    </li>
                </ul>

                <!-- Informasi Admin & Tombol Aksi di Kanan -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Tautan Melihat Halaman Utama Pelanggan -->
                    <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Lihat Web Pelanggan
                    </a>
                    <!-- Informasi Admin yang Sedang Aktif -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2 px-3 border" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user text-primary fa-lg"></i>
                            <span class="fw-semibold small"><?= htmlspecialchars($_SESSION['admin_fullname']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            <li><span class="dropdown-item-text small text-muted">Login sebagai: <strong>@<?= htmlspecialchars($_SESSION['admin_username']); ?></strong></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger fw-semibold" href="../logout.php">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar (Logout)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Kontainer Pembungkus Konten Halaman Admin -->
    <main class="py-4">
        <div class="container">
