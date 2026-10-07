<!DOCTYPE html>
{{-- Deklarasi HTML5 untuk halaman login admin --}}
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Laundry Express</title>
    
    {{-- Memuat Bootstrap 5 dan Font Awesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
</head>
<body>

    <div class="login-card">
        {{-- Header Form Login --}}
        <div class="login-header">
            <div class="mb-3">
                <i class="fa-solid fa-user-shield fa-3x"></i>
            </div>
            <h4 class="fw-bold mb-1">Panel Administrator</h4>
            <p class="mb-0 small opacity-75">Sistem Manajemen Layanan Laundry Express (Laravel)</p>
        </div>

        <div class="p-4 p-md-5">
            {{-- Menampilkan pesan error validasi login jika ada --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Menampilkan pesan flash sukses jika ada --}}
            @if(session('success'))
            <div class="alert alert-info rounded-3 small py-2 mb-4" role="alert">
                <i class="fa-solid fa-info-circle me-1"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Form Login POST dengan CSRF Token --}}
            <form method="POST" action="{{ route('login.process') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username', 'admin') }}" required autofocus>
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

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-decoration-none small text-muted">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda Pelanggan
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
