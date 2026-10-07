{{-- Mewarisi kerangka layout panel admin --}}
@extends('layouts.admin')

{{-- Menentukan judul tab halaman --}}
@section('title', 'Transaksi & Progres Cuci')

@section('content')

{{-- Header Halaman Transaksi --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Transaksi & Progres Cuci</h3>
        <p class="text-muted small mb-0">Pantau pesanan pelanggan, rincian biaya, dan perbarui progres pengerjaan cucian secara berkala.</p>
    </div>
</div>

{{-- Bar Filter Kategori Status & Pencarian Transaksi --}}
<div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
    <div class="row g-2 align-items-center">
        {{-- Tombol Filter Status --}}
        <div class="col-lg-6">
            <div class="btn-group btn-group-sm w-100 flex-wrap" role="group">
                <a href="{{ route('admin.orders.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 m-1">
                    Semua
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'Antrean']) }}" class="btn {{ request('status') === 'Antrean' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} rounded-pill px-3 m-1">
                    <i class="fa-solid fa-clock me-1"></i> Antrean
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'Dicuci']) }}" class="btn {{ request('status') === 'Dicuci' ? 'btn-info text-white' : 'btn-outline-info text-dark' }} rounded-pill px-3 m-1">
                    <i class="fa-solid fa-jug-detergent me-1"></i> Sedang Dicuci
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'Selesai']) }}" class="btn {{ request('status') === 'Selesai' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3 m-1">
                    <i class="fa-solid fa-circle-check me-1"></i> Selesai
                </a>
            </div>
        </div>
        {{-- Kolom Input Pencarian --}}
        <div class="col-lg-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex gap-2">
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari nama, No. Nota, WhatsApp..." value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                @if(request('q') || request('status'))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </form>
        </div>
    </div>
</div>

{{-- Tabel Daftar Transaksi Pesanan --}}
<div class="table-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Nota</th>
                    <th>Nama Pelanggan</th>
                    <th>No. WhatsApp</th>
                    <th>Rincian Cucian</th>
                    <th>Total Biaya</th>
                    <th>Status Saat Ini</th>
                    <th>Ubah Status</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $ord)
                <tr>
                    {{-- Nomor Nota Transaksi --}}
                    <td class="fw-bold text-primary">#{{ $ord->id }}</td>
                    
                    {{-- Nama Pelanggan & Waktu Order --}}
                    <td class="fw-semibold text-dark">
                        {{ $ord->customer_name }}
                        <div class="text-muted small">{{ $ord->created_at->format('d/m/Y H:i') }}</div>
                    </td>

                    {{-- Kontak WhatsApp & Direct Chat --}}
                    <td>
                        @php
                            $waNum = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $ord->whatsapp_number));
                            $waChat = "Halo {$ord->customer_name}, info update cucian Anda (Nota #{$ord->id}) saat ini berstatus: {$ord->status}.";
                        @endphp
                        <a href="https://wa.me/{{ $waNum }}?text={{ urlencode($waChat) }}" target="_blank" class="text-success text-decoration-none small fw-semibold">
                            <i class="fa-brands fa-whatsapp me-1"></i>{{ $ord->whatsapp_number }}
                        </a>
                    </td>

                    {{-- Rincian Layanan Cucian --}}
                    <td>
                        <ul class="list-unstyled mb-0 small">
                            @foreach ($ord->orderDetails as $item)
                                <li>
                                    <i class="fa-solid fa-angle-right text-muted me-1"></i>
                                    {{ $item->service->service_name ?? 'Layanan' }}
                                    <span class="badge bg-light text-dark border ms-1">{{ $item->qty_or_weight }} {{ $item->service->unit_type ?? 'Unit' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    {{-- Total Biaya Pesanan --}}
                    <td class="fw-bold text-dark fs-6">
                        Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                    </td>

                    {{-- Badge Status Pengerjaan --}}
                    <td>
                        @php
                            $bStatus = 'bg-warning text-dark';
                            if ($ord->status === 'Dicuci') $bStatus = 'bg-info text-white';
                            if ($ord->status === 'Selesai') $bStatus = 'bg-success text-white';
                        @endphp
                        <span class="badge {{ $bStatus }} rounded-pill px-3 py-2 fw-bold">
                            {{ $ord->status }}
                        </span>
                    </td>

                    {{-- Form Cepat Update Status Cuci ('Antrean' -> 'Dicuci' -> 'Selesai') Sesuai Modul --}}
                    <td>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $ord->id) }}" class="d-flex gap-1 align-items-center">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                <option value="Antrean" {{ $ord->status === 'Antrean' ? 'selected' : '' }}>Antrean</option>
                                <option value="Dicuci" {{ $ord->status === 'Dicuci' ? 'selected' : '' }}>Dicuci</option>
                                <option value="Selesai" {{ $ord->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </form>
                    </td>

                    {{-- Tombol Aksi: Cetak Struk dan Hapus Order --}}
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('orders.show', $ord->id) }}" target="_blank" class="btn btn-outline-primary" title="Cetak Nota">
                                <i class="fa-solid fa-receipt"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="if(confirm('Yakin ingin menghapus transaksi ini?')) { document.getElementById('hapusOrder{{ $ord->id }}').submit(); }" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>

                        {{-- Form Delete Order --}}
                        <form id="hapusOrder{{ $ord->id }}" method="POST" action="{{ route('admin.orders.destroy', $ord->id) }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Data transaksi pesanan tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
