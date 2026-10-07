{{-- Mewarisi kerangka layout panel admin --}}
@extends('layouts.admin')

{{-- Menentukan judul tab browser --}}
@section('title', 'Dashboard Ringkasan')

@section('content')

{{-- Header Sambutan Dashboard --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Pengelola Laundry</h3>
        <p class="text-muted small mb-0">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Pantau transaksi dan status pengerjaan cuci.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.services.index') }}" class="btn btn-primary rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Kelola Layanan
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-list me-1"></i> Semua Pesanan
        </a>
    </div>
</div>

{{-- Kartu Statistik Indikator Kinerja --}}
<div class="row g-3 mb-4">
    {{-- Kartu 1: Total Pendapatan --}}
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Total Pendapatan</span>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-3"><i class="fa-solid fa-money-bill-wave fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
            <span class="text-success small fw-semibold"><i class="fa-solid fa-arrow-trend-up me-1"></i>Dari {{ $totalOrders }} transaksi</span>
        </div>
    </div>
    {{-- Kartu 2: Pesanan Dalam Antrean --}}
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Antrean Masuk</span>
                <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3"><i class="fa-solid fa-clock fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $totalAntrean }} Pesanan</h4>
            <span class="text-warning small fw-semibold"><i class="fa-solid fa-hourglass-start me-1"></i>Menunggu dicuci</span>
        </div>
    </div>
    {{-- Kartu 3: Sedang Dicuci --}}
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Sedang Dicuci</span>
                <div class="p-2 bg-info bg-opacity-10 text-info rounded-3"><i class="fa-solid fa-jug-detergent fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $totalDicuci }} Pesanan</h4>
            <span class="text-info small fw-semibold"><i class="fa-solid fa-spinner fa-spin me-1"></i>Dalam pengerjaan</span>
        </div>
    </div>
    {{-- Kartu 4: Selesai Dikerjakan --}}
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Cucian Selesai</span>
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3"><i class="fa-solid fa-circle-check fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $totalSelesai }} Pesanan</h4>
            <span class="text-primary small fw-semibold"><i class="fa-solid fa-box-check me-1"></i>Siap diambil</span>
        </div>
    </div>
</div>

{{-- Tabel 5 Pesanan Masuk Terbaru --}}
<div class="table-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Pesanan Masuk Terbaru</h5>
            <span class="text-muted small">5 transaksi terakhir yang didaftarkan pelanggan</span>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small fw-bold text-primary">Lihat Selengkapnya <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Nota</th>
                    <th>Nama Pelanggan</th>
                    <th>No. WhatsApp</th>
                    <th>Total Biaya</th>
                    <th>Status Cuci</th>
                    <th>Waktu Daftar</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $ord)
                <tr>
                    <td class="fw-bold text-primary">#{{ $ord->id }}</td>
                    <td class="fw-semibold text-dark">{{ $ord->customer_name }}</td>
                    <td>
                        @php
                            $numWa = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $ord->whatsapp_number));
                        @endphp
                        <a href="https://wa.me/{{ $numWa }}" target="_blank" class="text-success text-decoration-none small fw-semibold">
                            <i class="fa-brands fa-whatsapp me-1"></i>{{ $ord->whatsapp_number }}
                        </a>
                    </td>
                    <td class="fw-bold text-dark">Rp {{ number_format($ord->total_price, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $stCls = 'bg-warning text-dark';
                            if ($ord->status === 'Dicuci') $stCls = 'bg-info text-white';
                            if ($ord->status === 'Selesai') $stCls = 'bg-success text-white';
                        @endphp
                        <span class="badge {{ $stCls }} rounded-pill px-3 py-1">
                            {{ $ord->status }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('orders.show', $ord->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Lihat Struk">
                            <i class="fa-solid fa-receipt me-1"></i> Struk
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi pesanan yang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
