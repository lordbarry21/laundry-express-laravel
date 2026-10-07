{{-- Mewarisi kerangka layout panel admin --}}
@extends('layouts.admin')

{{-- Menentukan judul tab halaman --}}
@section('title', 'Kelola Tarif & Layanan')

@section('content')

{{-- Header Halaman Kelola Layanan --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Tarif & Paket Layanan</h3>
        <p class="text-muted small mb-0">Kelola master data layanan cuci kiloan, satuan, dan express (CRUD) sesuai ketentuan modul.</p>
    </div>
    {{-- Tombol Pemicu Modal Tambah Layanan --}}
    <button type="button" class="btn btn-primary rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahLayanan">
        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Layanan Baru
    </button>
</div>

{{-- Tabel Daftar Master Layanan --}}
<div class="table-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Nama Layanan</th>
                    <th>Kategori</th>
                    <th>Tarif / Harga</th>
                    <th>Satuan</th>
                    <th>Deskripsi</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $srv)
                <tr>
                    <td class="fw-bold text-muted">{{ $srv->id }}</td>
                    <td class="fw-bold text-dark">{{ $srv->service_name }}</td>
                    <td>
                        @php
                            $cBadge = 'bg-info bg-opacity-10 text-info';
                            if ($srv->category === 'Satuan') $cBadge = 'bg-warning bg-opacity-10 text-warning';
                            if ($srv->category === 'Express') $cBadge = 'bg-danger bg-opacity-10 text-danger';
                        @endphp
                        <span class="badge {{ $cBadge }} rounded-pill px-3 py-1 fw-bold">
                            {{ $srv->category }}
                        </span>
                    </td>
                    <td class="fw-bold text-primary">Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">
                            Per {{ $srv->unit_type }}
                        </span>
                    </td>
                    <td class="small text-muted" style="max-width: 250px;">{{ $srv->description ?? '-' }}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            {{-- Tombol Buka Modal Edit --}}
                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $srv->id }}" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            {{-- Tombol Hapus Layanan --}}
                            <button type="button" class="btn btn-outline-danger" onclick="if(confirm('Yakin ingin menghapus layanan ini?')) { document.getElementById('formHapus{{ $srv->id }}').submit(); }" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>

                        {{-- Form Hidden untuk Delete DELETE HTTP Method --}}
                        <form id="formHapus{{ $srv->id }}" method="POST" action="{{ route('admin.services.destroy', $srv->id) }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>

                        {{-- Modal Edit Layanan Ini --}}
                        <div class="modal fade" id="modalEdit{{ $srv->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered text-start">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">
                                            <i class="fa-solid fa-pen-to-square text-warning me-2"></i>Edit Paket Layanan
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.services.update', $srv->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body pt-3">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-secondary">Nama Layanan</label>
                                                <input type="text" name="service_name" class="form-control" value="{{ $srv->service_name }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold text-secondary">Kategori</label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="Kiloan" {{ $srv->category === 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                                                        <option value="Satuan" {{ $srv->category === 'Satuan' ? 'selected' : '' }}>Satuan</option>
                                                        <option value="Express" {{ $srv->category === 'Express' ? 'selected' : '' }}>Express</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold text-secondary">Satuan</label>
                                                    <select name="unit_type" class="form-select" required>
                                                        <option value="Kg" {{ $srv->unit_type === 'Kg' ? 'selected' : '' }}>Kg</option>
                                                        <option value="Pcs" {{ $srv->unit_type === 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-secondary">Tarif Satuan (Rp)</label>
                                                <input type="number" name="price_per_unit" class="form-control" value="{{ $srv->price_per_unit }}" min="0" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-secondary">Deskripsi</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $srv->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada paket layanan. Silakan klik tombol 'Tambah Layanan Baru'.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Layanan Baru --}}
<div class="modal fade" id="modalTambahLayanan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-circle-plus text-primary me-2"></i>Tambah Layanan Laundry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.services.store') }}">
                @csrf
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Layanan / Paket Cuci</label>
                        <input type="text" name="service_name" class="form-control" placeholder="Contoh: Cuci Karpet Tebal" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="Kiloan">Kiloan</option>
                                <option value="Satuan">Satuan</option>
                                <option value="Express">Express</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Jenis Satuan</label>
                            <select name="unit_type" class="form-select" required>
                                <option value="Kg">Kg (Kilogram)</option>
                                <option value="Pcs">Pcs (Satuan)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tarif Harga Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" placeholder="Contoh: 10000" min="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Deskripsi Paket Layanan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan detail proses dan estimasi waktu selesai..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
