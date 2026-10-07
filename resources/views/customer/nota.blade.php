{{-- Mewarisi kerangka layout publik pelanggan --}}
@extends('layouts.app')

{{-- Menentukan judul halaman nota --}}
@section('title', 'Nota Pemesanan #' . $order->id . ' - Laundry Express')

{{-- Menambahkan CSS kustom untuk kuitansi nota dan mode cetak fisik --}}
@push('styles')
<style>
    .nota-card {
        max-width: 720px;
        margin: 30px auto;
        background: #ffffff;
        border-radius: var(--radius-main);
        border: 1px solid var(--brand-border);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .nota-card-header {
        background-color: var(--brand-dark);
        color: #ffffff;
        padding: 28px;
    }
    /* Mode cetak bersih tanpa tombol dan navigasi */
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        .site-nav, footer { display: none !important; }
        .nota-card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
            margin: 0 !important;
            max-width: 100% !important;
            border-radius: 0 !important;
        }
        .nota-card-header {
            background-color: #000000 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@section('content')
<div class="container pb-5">
    {{-- Memformat nomor WhatsApp berawalan kode 62 untuk integrasi link chat --}}
    @php
        $noWa = preg_replace('/[^0-9]/', '', $order->whatsapp_number);
        if (substr($noWa, 0, 1) === '0') {
            $noWa = '62' . substr($noWa, 1);
        }
        $pesanWa = "Halo *{$order->customer_name}*,\n\n"
                 . "Terima kasih telah memesan di *Laundry Express*.\n"
                 . "Nota Transaksi: #{$order->id}\n"
                 . "Status: {$order->status}\n"
                 . "Total Biaya: Rp " . number_format($order->total_price, 0, ',', '.') . "\n\n"
                 . "Pakaian Anda segera diproses bersih & wangi. Terima kasih!";
        $linkWa = "https://api.whatsapp.com/send?phone={$noWa}&text=" . urlencode($pesanWa);
    @endphp

    {{-- Tombol Aksi di Atas Nota (Kembali, Kirim WhatsApp, Cetak Struk) --}}
    <div class="d-flex justify-content-between align-items-center mx-auto mt-4 mb-3 no-print" style="max-width: 720px;">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda
        </a>
        <div class="d-flex gap-2">
            <a href="{{ $linkWa }}" target="_blank" class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> Kirim WhatsApp
            </a>
            <button onclick="window.print()" class="btn btn-action-primary rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Struk
            </button>
        </div>
    </div>

    {{-- Kartu Dokumen Kuitansi Nota Transaksi --}}
    <div class="nota-card">
        {{-- Header Nota --}}
        <div class="nota-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-soap me-2"></i>LAUNDRY EXPRESS</h5>
                    <p class="mb-0 opacity-75 small">Layanan Cuci Bersih, Kering & Setrika Kilat</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill fs-6 mb-1">
                        NOTA #{{ $order->id }}
                    </span>
                    <div class="small opacity-75">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Isi Rincian Nota --}}
        <div class="p-4">
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <span class="text-muted small text-uppercase fw-bold">Pelanggan:</span>
                    <h5 class="fw-bold text-dark mt-1 mb-1">{{ $order->customer_name }}</h5>
                    <p class="text-secondary small mb-0">
                        <i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $order->whatsapp_number }}
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <span class="text-muted small text-uppercase fw-bold">Status Cuci:</span>
                    <div class="mt-1">
                        @php
                            $statusClass = 'bg-secondary text-white';
                            if ($order->status === 'Dicuci') $statusClass = 'bg-primary text-white';
                            if ($order->status === 'Selesai') $statusClass = 'bg-success text-white';
                        @endphp
                        <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill fs-6 fw-bold">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tabel Rincian Item Transaksi --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Paket Layanan</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-end">Tarif Satuan</th>
                            <th class="text-center">Berat / Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->service->service_name ?? 'Layanan' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-secondary border rounded-pill">
                                    {{ $item->service->category ?? 'Kiloan' }}
                                </span>
                            </td>
                            <td class="text-end text-muted">
                                Rp {{ number_format($item->service->price_per_unit ?? 0, 0, ',', '.') }} / {{ $item->service->unit_type ?? 'Unit' }}
                            </td>
                            <td class="text-center fw-bold">
                                {{ $item->qty_or_weight }} {{ $item->service->unit_type ?? 'Unit' }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold py-3 fs-5">TOTAL BIAYA:</td>
                            <td class="text-end fw-bold text-dark py-3 fs-5">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Ketentuan Pengambilan Cucian --}}
            <div class="p-3 bg-light rounded-3 border small text-muted">
                <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-exclamation text-dark me-1"></i> Catatan & Ketentuan Laundry:</h6>
                <ul class="mb-0 ps-3">
                    <li>Pengambilan cucian wajib menunjukkan bukti kuitansi nota ini.</li>
                    <li>Estimasi pengerjaan reguler selesai 2-3 hari kerja, paket express selesai 3 jam.</li>
                    <li>Pakaian yang tidak diambil lebih dari 30 hari di luar tanggung jawab pihak laundry.</li>
                </ul>
            </div>

            {{-- Kolom Tanda Tangan Fisik --}}
            <div class="row mt-4 pt-3 text-center">
                <div class="col-6">
                    <div class="small text-muted mb-5">Petugas Kasir</div>
                    <div class="fw-bold text-dark">( .................................. )</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted mb-5">Pelanggan</div>
                    <div class="fw-bold text-dark">( {{ $order->customer_name }} )</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
