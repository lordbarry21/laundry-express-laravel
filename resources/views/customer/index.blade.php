{{-- Mewarisi kerangka layout publik pelanggan yang telah distandarisasi --}}
@extends('layouts.app')

{{-- Menentukan judul tab browser --}}
@section('title', 'Laundry Express - Layanan Cuci Kilat & Kiloan')

{{-- Bagian konten utama halaman beranda --}}
@section('content')

    {{-- HERO SECTION: Asymmetric Split Hero yang muat dalam viewport awal --}}
    <section class="py-5 bg-white border-bottom" id="beranda">
        <div class="container py-lg-4">
            <div class="row align-items-center gy-4">
                {{-- Kolom teks utama (kiri) --}}
                <div class="col-lg-7">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="fa-solid fa-bolt me-1"></i> Cuci Kilat & Kiloan Bersih
                    </span>
                    <h1 class="display-5 fw-extrabold text-dark tracking-tight mb-3">
                        Pesan Jasa Laundry Cepat, Rapi dan Tepat Waktu.
                    </h1>
                    <p class="lead text-secondary mb-4" style="max-width: 58ch;">
                        Catat cucian kiloan pakaian, jas formal, atau paket kilat 3 jam dengan estimasi biaya transparan dan nota digital langsung.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#pesan" class="btn btn-action-primary px-4 py-3 rounded-pill shadow-sm">
                            <i class="fa-solid fa-basket-shopping me-2"></i> Pesan Cuci Sekarang
                        </a>
                        <a href="#tracking" class="btn btn-outline-secondary px-4 py-3 rounded-pill fw-semibold">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Lacak Status Cucian
                        </a>
                    </div>
                </div>
                {{-- Kolom ringkasan visual garansi layanan (kanan) --}}
                <div class="col-lg-5">
                    <div class="card-surface p-4 border bg-light bg-opacity-50">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Standar Mutu Pengerjaan:</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-white text-dark border p-2 rounded-3"><i class="fa-solid fa-scale-balanced fa-lg text-primary"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Timbang Transparan</h6>
                                    <p class="small text-secondary mb-0">Penghitungan tarif per Kg atau per Pcs tanpa biaya tersembunyi.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-white text-dark border p-2 rounded-3"><i class="fa-solid fa-stopwatch fa-lg text-primary"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Pilihan Paket Kilat 3 Jam</h6>
                                    <p class="small text-secondary mb-0">Prioritas pengerjaan express untuk kebutuhan seragam dan pakaian mendesak.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <span class="badge bg-white text-dark border p-2 rounded-3"><i class="fa-solid fa-receipt fa-lg text-primary"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Nota WhatsApp Otomatis</h6>
                                    <p class="small text-secondary mb-0">Struk kuitansi dan status pengerjaan langsung terhubung ke nomor Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Alert error jika validasi form gagal --}}
    @if(session('error'))
    <div class="container mt-4">
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 mb-0" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

    {{-- SEKSI 1: KATALOG TARIF & MASTER LAYANAN --}}
    <section class="py-5" id="layanan">
        <div class="container">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Daftar Tarif & Paket Layanan</h3>
                <p class="text-secondary small mb-0">Pilih layanan cuci kiloan, satuan atau dry cleaning sesuai kebutuhan pakaian Anda.</p>
            </div>

            {{-- Grid bento kartu paket layanan --}}
            <div class="row g-3">
                @foreach ($services as $srv)
                <div class="col-md-6 col-lg-4">
                    <div class="card card-surface h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                @php
                                    $badgeStyle = 'bg-light text-dark border';
                                    if ($srv->category === 'Express') $badgeStyle = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    if ($srv->category === 'Satuan') $badgeStyle = 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25';
                                @endphp
                                <span class="badge {{ $badgeStyle }} rounded-pill px-3 py-1 fw-bold small">
                                    {{ $srv->category }}
                                </span>
                                <span class="text-muted small fw-semibold">
                                    Per {{ $srv->unit_type }}
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">{{ $srv->service_name }}</h5>
                            <p class="text-secondary small mb-3">{{ $srv->description ?? 'Pencucian higienis dan pewangi segar.' }}</p>
                        </div>
                        <div class="pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="fs-4 fw-extrabold text-dark">Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }}</span>
                                <span class="text-muted small">/ {{ $srv->unit_type }}</span>
                            </div>
                            <a href="#pesan" onclick="pilihPaket('{{ $srv->id }}')" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-semibold py-2">
                                <i class="fa-solid fa-plus me-1"></i> Pilih Paket Ini
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SEKSI 2: FORM PENDAFTARAN & PEMESANAN LAYANAN --}}
    <section class="py-5 bg-white border-top border-bottom" id="pesan">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card-surface p-4 p-md-5">
                        <div class="mb-4">
                            <h3 class="fw-bold text-dark mb-1">Formulir Pemesanan Cuci</h3>
                            <p class="text-secondary small mb-0">Lengkapi identitas dan tentukan perkiraan berat atau jumlah pakaian.</p>
                        </div>

                        {{-- Form POST ke orders.store dengan CSRF Token --}}
                        <form method="POST" action="{{ route('orders.store') }}" id="formOrder">
                            @csrf

                            {{-- Baris Identitas Pelanggan --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Nama Lengkap Pelanggan</label>
                                    <input type="text" name="customer_name" class="form-control rounded-3 py-2 @error('customer_name') is-invalid @enderror" placeholder="Contoh: Rian Pratama" value="{{ old('customer_name') }}" required>
                                    @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Nomor WhatsApp Aktif</label>
                                    <input type="tel" name="whatsapp_number" class="form-control rounded-3 py-2 @error('whatsapp_number') is-invalid @enderror" placeholder="Contoh: 081234567890" value="{{ old('whatsapp_number') }}" required>
                                    <div class="form-text small text-muted">Nota transaksi akan dikirimkan ke nomor ini.</div>
                                    @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4 text-muted">

                            {{-- Pilihan Layanan & Berat/Unit --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Rincian Layanan Cucian</h6>
                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill fw-semibold" onclick="tambahBarisItem()">
                                    <i class="fa-solid fa-plus me-1"></i> Tambah Layanan Lain
                                </button>
                            </div>

                            {{-- Baris Item Pilihan Layanan Dinamis --}}
                            <div id="container-items" class="d-flex flex-column gap-3 mb-4">
                                <div class="row g-2 align-items-end p-3 rounded-3 bg-light border item-row">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold small text-secondary">Paket Layanan</label>
                                        <select name="service_id[]" class="form-select select-service" onchange="hitungTotal()" required>
                                            <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                                            @foreach ($services as $srv)
                                            <option value="{{ $srv->id }}" data-harga="{{ $srv->price_per_unit }}" data-satuan="{{ $srv->unit_type }}">
                                                {{ $srv->service_name }} (Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }} / {{ $srv->unit_type }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small text-secondary">Berat / Jumlah</label>
                                        <div class="input-group">
                                            <input type="number" name="qty_or_weight[]" class="form-control input-qty" value="1" min="1" oninput="hitungTotal()" required>
                                            <span class="input-group-text span-satuan bg-white">Unit</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small text-secondary">Subtotal</label>
                                        <input type="text" class="form-control bg-white input-subtotal fw-bold text-dark" value="Rp 0" readonly>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="hapusBarisItem(this)" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Ringkasan Biaya Akumulasi --}}
                            <div class="p-3 rounded-3 bg-light border mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-secondary small d-block">Estimasi Total Biaya:</span>
                                        <h4 class="fw-extrabold text-dark mb-0" id="tampilan-total">Rp 0</h4>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2 small">
                                        Status Awal: Antrean
                                    </span>
                                </div>
                            </div>

                            {{-- Tombol Submit Form Pemesanan --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-action-primary py-3 rounded-pill fw-bold">
                                    Konfirmasi & Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SEKSI 3: LACAK STATUS PESANAN (Tracking Progress Cuci) --}}
    <section class="py-5" id="tracking">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="mb-4 text-center">
                        <h3 class="fw-bold text-dark mb-1">Lacak Status Cucian</h3>
                        <p class="text-secondary small mb-0">Cek status pengerjaan melalui nomor ID Nota atau nomor WhatsApp Anda.</p>
                    </div>

                    {{-- Form Pencarian Tracking Status --}}
                    <div class="card-surface p-3 mb-4">
                        <form method="GET" action="{{ route('home') }}#tracking" class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-search"></i></span>
                                    <input type="text" name="keyword_tracking" class="form-control border-start-0" placeholder="Nomor Nota (contoh: 1) atau No. WhatsApp..." value="{{ request('keyword_tracking') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-action-primary w-100 rounded-3 py-2 fw-semibold">
                                    Cek Status
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Menampilkan Hasil Pelacakan --}}
                    @if($ordersTrack !== null)
                        @if($ordersTrack->count() > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($ordersTrack as $order)
                                    <div class="card-surface p-4">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">Nota #{{ $order->id }} - {{ $order->customer_name }}</h6>
                                                <span class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @php
                                                $badgeStatus = 'bg-secondary text-white';
                                                $progPercent = 33;
                                                if ($order->status === 'Dicuci') {
                                                    $badgeStatus = 'bg-primary text-white';
                                                    $progPercent = 66;
                                                } elseif ($order->status === 'Selesai') {
                                                    $badgeStatus = 'bg-success text-white';
                                                    $progPercent = 100;
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeStatus }} px-3 py-2 rounded-pill fw-bold">
                                                {{ $order->status }}
                                            </span>
                                        </div>

                                        {{-- Timeline Progress --}}
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between text-muted small fw-semibold mb-1">
                                                <span>1. Antrean</span>
                                                <span>2. Dicuci</span>
                                                <span>3. Selesai</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $progPercent }}%"></div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2">
                                            <span class="fw-bold text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                                                Lihat Nota
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="card-surface p-4 text-center">
                                <h6 class="fw-bold text-dark mb-1">Data Cucian Tidak Ditemukan</h6>
                                <p class="text-muted small mb-0">Pastikan ID Nota atau Nomor WhatsApp yang dimasukkan sudah benar.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection

{{-- Skrip JavaScript untuk kalkulasi harga otomatis --}}
@push('scripts')
<script>
    // Menyimpan data master paket layanan dalam variabel JavaScript
    const masterLayanan = @json($services);

    // Fungsi untuk menambahkan baris layanan baru
    function tambahBarisItem() {
        const container = document.getElementById('container-items');
        const div = document.createElement('div');
        div.className = 'row g-2 align-items-end p-3 rounded-3 bg-light border item-row';
        
        let optionsHtml = '<option value="" disabled selected>-- Pilih Jenis Layanan --</option>';
        masterLayanan.forEach(item => {
            optionsHtml += `<option value="${item.id}" data-harga="${item.price_per_unit}" data-satuan="${item.unit_type}">
                ${item.service_name} (Rp ${parseInt(item.price_per_unit).toLocaleString('id-ID')} / ${item.unit_type})
            </option>`;
        });

        div.innerHTML = `
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-secondary">Paket Layanan</label>
                <select name="service_id[]" class="form-select select-service" onchange="hitungTotal()" required>
                    ${optionsHtml}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Berat / Jumlah</label>
                <div class="input-group">
                    <input type="number" name="qty_or_weight[]" class="form-control input-qty" value="1" min="1" oninput="hitungTotal()" required>
                    <span class="input-group-text span-satuan bg-white">Unit</span>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Subtotal</label>
                <input type="text" class="form-control bg-white input-subtotal fw-bold text-dark" value="Rp 0" readonly>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-outline-danger w-100" onclick="hapusBarisItem(this)" title="Hapus">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
    }

    // Fungsi untuk menghapus baris item
    function hapusBarisItem(btn) {
        const semuaBaris = document.querySelectorAll('.item-row');
        if (semuaBaris.length > 1) {
            btn.closest('.item-row').remove();
            hitungTotal();
        } else {
            alert('Minimal harus memilih 1 paket layanan!');
        }
    }

    // Fungsi menghitung total harga
    function hitungTotal() {
        let totalSemua = 0;
        const barisItems = document.querySelectorAll('.item-row');

        barisItems.forEach(baris => {
            const selectSrv = baris.querySelector('.select-service');
            const inputQty = baris.querySelector('.input-qty');
            const inputSubtotal = baris.querySelector('.input-subtotal');
            const spanSatuan = baris.querySelector('.span-satuan');

            const opsiTerpilih = selectSrv.options[selectSrv.selectedIndex];

            if (opsiTerpilih && opsiTerpilih.value) {
                const harga = parseFloat(opsiTerpilih.getAttribute('data-harga')) || 0;
                const satuan = opsiTerpilih.getAttribute('data-satuan') || 'Unit';
                const qty = parseFloat(inputQty.value) || 0;

                spanSatuan.textContent = satuan;
                const subtotal = harga * qty;
                inputSubtotal.value = 'Rp ' + subtotal.toLocaleString('id-ID');
                totalSemua += subtotal;
            } else {
                inputSubtotal.value = 'Rp 0';
                spanSatuan.textContent = 'Unit';
            }
        });

        document.getElementById('tampilan-total').textContent = 'Rp ' + totalSemua.toLocaleString('id-ID');
    }

    // Fungsi memilih paket dari kartu katalog
    function pilihPaket(id) {
        const barisPertama = document.querySelector('.item-row');
        if (barisPertama) {
            const select = barisPertama.querySelector('.select-service');
            select.value = id;
            hitungTotal();
        }
    }
</script>
@endpush
