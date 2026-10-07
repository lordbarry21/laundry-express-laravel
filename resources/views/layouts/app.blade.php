<!DOCTYPE html>
{{-- Deklarasi dokumen HTML5 standar --}}
<html lang="id">
<head>
    {{-- Pengaturan pengkodean karakter dokumen UTF-8 --}}
    <meta charset="UTF-8">
    {{-- Pengaturan viewport responsif dengan viewport stability min-h-[100dvh] --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman dinamis dari child view --}}
    <title>@yield('title', 'Laundry Express - Layanan Cuci Kilat & Kiloan')</title>

    {{-- Memuat Bootstrap 5.3 dari CDN sebagai fondasi layout responsif --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Memuat ikon Font Awesome 6 dari CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    {{-- Memuat jenis huruf Plus Jakarta Sans untuk tipografi profesional --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Kustomisasi CSS berbasis sistem token yang konsisten (Anti-Slop Design System) --}}
    <style>
        :root {
            /* Token warna primer dan netral berstandar kontras tinggi */
            --brand-primary: #0284c7;
            --brand-dark: #0f172a;
            --brand-slate: #334155;
            --brand-surface: #ffffff;
            --brand-bg: #f8fafc;
            --brand-border: #e2e8f0;
            --radius-main: 16px;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--brand-bg);
            color: var(--brand-dark);
            min-height: 100dvh;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }
        /* Navigasi satu baris tetap proporsional (tinggi 68px) */
        .site-nav {
            height: 68px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--brand-border);
            z-index: 1000;
        }
        /* Tombol aksi dengan taktil feedback */
        .btn-action-primary {
            background-color: var(--brand-dark);
            color: #ffffff;
            border: none;
            font-weight: 600;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }
        .btn-action-primary:hover {
            background-color: #1e293b;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-action-primary:active {
            transform: translateY(1px);
        }
        /* Kartu dengan shadow halus bernuansa netral */
        .card-surface {
            background: var(--brand-surface);
            border: 1px solid var(--brand-border);
            border-radius: var(--radius-main);
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.03), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- NAVBAR: Menu Navigasi Pengunjung (Single Line Desktop) --}}
    <nav class="navbar navbar-expand-lg site-nav sticky-top py-0">
        <div class="container h-100">
            {{-- Brand logo dan nama sistem laundry --}}
            <a class="navbar-brand fw-extrabold text-dark d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-3 p-2" style="width: 34px; height: 34px;">
                    <i class="fa-solid fa-soap fa-sm"></i>
                </span>
                <span class="fs-5 tracking-tight fw-bold">Laundry<span class="text-primary">Express</span></span>
            </a>

            {{-- Tombol toggler menu responsif mobile --}}
            <button class="navbar-toggler border-0 shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars fa-lg text-dark"></i>
            </button>

            {{-- Tautan menu navigasi pelanggan --}}
            <div class="collapse navbar-collapse h-100" id="navMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link fw-semibold px-3 text-secondary" href="{{ route('home') }}#layanan">Tarif & Layanan</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3 text-secondary" href="{{ route('home') }}#pesan">Pesan Cuci</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3 text-secondary" href="{{ route('home') }}#tracking">Lacak Status</a></li>
                    <li class="nav-item ms-lg-3">
                        {{-- Tombol akses masuk panel admin dengan kontras tinggi --}}
                        <a class="btn btn-outline-dark btn-sm px-3 py-2 rounded-pill fw-semibold" href="{{ route('login') }}">
                            <i class="fa-solid fa-lock me-1"></i> Masuk Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Konten Utama Halaman Pelanggan --}}
    <main>
        @yield('content')
    </main>

    {{-- Kaki Halaman Pelanggan Bersih & Fungsional --}}
    <footer class="bg-white border-top py-4 mt-5">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark">Laundry Express</span>
                    <span class="text-muted small">| Layanan Cuci Kiloan, Satuan & Express</span>
                </div>
                <div class="text-muted small">
                    &copy; {{ date('Y') }} Sistem Operasional Laundry
                </div>
            </div>
        </div>
    </footer>

    {{-- Memuat bundle javascript Bootstrap 5 dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
