<!DOCTYPE html>
{{-- Deklarasi dokumen HTML5 untuk panel admin --}}
<html lang="id">
<head>
    {{-- Pengaturan pengkodean karakter dokumen UTF-8 --}}
    <meta charset="UTF-8">
    {{-- Pengaturan viewport responsif --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman dinamis untuk panel admin --}}
    <title>@yield('title', 'Panel Admin') - Laundry Express</title>

    {{-- Memuat stylesheet Bootstrap 5 dari CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Memuat ikon Font Awesome 6 dari CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    {{-- Memuat Google Fonts Plus Jakarta Sans --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Kustomisasi CSS tata letak halaman admin --}}
    <style>
        :root {
            --primary-admin: #0d6efd;
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
    @stack('styles')
</head>
<body>

    {{-- Navigasi Bar Utama Admin --}}
    <nav class="navbar navbar-expand-lg navbar-light admin-navbar sticky-top py-3">
        <div class="container">
            {{-- Brand logo dan nama panel admin --}}
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-soap text-primary"></i>
                <span>Admin<span class="text-dark">Laundry</span></span>
            </a>
            {{-- Tombol toggler mobile --}}
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            {{-- Daftar link navigasi admin --}}
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    {{-- Menu Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.dashboard') ? 'active-menu' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-chart-pie me-1"></i> Dashboard
                        </a>
                    </li>
                    {{-- Menu Master Layanan CRUD Sesuai Modul --}}
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.services.*') ? 'active-menu' : '' }}" href="{{ route('admin.services.index') }}">
                            <i class="fa-solid fa-tags me-1"></i> Kelola Tarif & Layanan
                        </a>
                    </li>
                    {{-- Menu Transaksi Pesanan & Update Status Sesuai Modul --}}
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('admin.orders.*') ? 'active-menu' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fa-solid fa-boxes-stacked me-1"></i> Transaksi & Progres Cuci
                        </a>
                    </li>
                </ul>

                {{-- Sisi Kanan: Tautan Web Pelanggan & Profil Admin --}}
                <div class="d-flex align-items-center gap-3">
                    {{-- Tautan untuk membuka halaman publik pelanggan --}}
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Lihat Web Pelanggan
                    </a>
                    {{-- Dropdown info akun admin --}}
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2 px-3 border" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user text-primary fa-lg"></i>
                            <span class="fw-semibold small">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            <li><span class="dropdown-item-text small text-muted">Login: <strong>{{ '@' . (Auth::user()->username ?? 'admin') }}</strong></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                {{-- Form POST untuk logout demi keamanan CSRF --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-semibold">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Konten Utama Halaman Admin --}}
    <main class="py-4">
        <div class="container">
            {{-- Menampilkan pesan flash alert sukses dari session jika ada --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Menampilkan pesan flash alert error dari session jika ada --}}
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Tempat menyuntikkan isi konten dari child view --}}
            @yield('content')
        </div>
    </main>

    {{-- Kaki Halaman Panel Admin --}}
    <footer class="py-4 text-center text-muted small border-top bg-white mt-auto">
        <div class="container">
            <p class="mb-0">
                &copy; {{ date('Y') }} <strong>Panel Admin Laundry Express (Laravel Framework)</strong> - Sesuai Modul Praktikum Kejuruan
            </p>
        </div>
    </footer>

    {{-- Memuat bundle javascript Bootstrap 5 dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
