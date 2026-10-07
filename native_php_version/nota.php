<?php
// Memasukkan file koneksi.php agar halaman ini dapat mengambil data transaksi dari database
require_once 'koneksi.php';

// Mengambil parameter ID pesanan dari query string URL, default 0 jika tidak ada
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Validasi jika ID tidak valid atau kurang dari sama dengan 0
if ($order_id <= 0) {
    // Menghentikan script dan menampilkan pesan error jika ID tidak sesuai
    exit('ID Nota pesanan tidak valid atau tidak ditemukan!');
}

// Menjalankan query untuk mengambil data header transaksi dari tabel orders berdasarkan ID pesanan
$query_order = mysqli_query($koneksi, "SELECT * FROM orders WHERE id = $order_id");
// Mengambil satu baris data transaksi ke dalam bentuk array asosiatif
$order = mysqli_fetch_assoc($query_order);

// Memeriksa apakah data pesanan ditemukan dalam database
if (! $order) {
    // Menghentikan script dan menampilkan pesan bila data pesanan tidak ada
    exit('Data pesanan laundry dengan nomor nota #'.$order_id.' tidak ditemukan!');
}

// Menjalankan query untuk mengambil seluruh detail item pesanan yang berelasi dengan tabel services
$query_details = mysqli_query($koneksi, "
    SELECT od.*, s.service_name, s.category, s.price_per_unit, s.unit_type 
    FROM order_details od 
    JOIN services s ON od.service_id = s.id 
    WHERE od.order_id = $order_id
");

// Menyiapkan array kosong untuk menampung rincian layanan pesanan
$items = [];
// Melakukan perulangan untuk mengambil setiap baris detail transaksi
while ($row = mysqli_fetch_assoc($query_details)) {
    // Menyimpan baris detail ke dalam array $items
    $items[] = $row;
}

// Format nomor WhatsApp agar berawalan kode negara 62 untuk integrasi link chat WhatsApp
$no_wa = preg_replace('/[^0-9]/', '', $order['whatsapp_number']);
// Memeriksa jika nomor berawalan angka '0', ubah menjadi '62'
if (substr($no_wa, 0, 1) === '0') {
    $no_wa = '62'.substr($no_wa, 1);
}

// Menyusun template pesan konfirmasi WhatsApp yang rapi
$pesan_wa = 'Halo *'.$order['customer_name']."*,\n\n";
$pesan_wa .= "Terima kasih telah mempercayakan pakaian Anda di *Laundry Express*.\n";
$pesan_wa .= "Berikut rincian pesanan cuci Anda:\n";
$pesan_wa .= "------------------------------------\n";
$pesan_wa .= '📄 *No. Nota:* #'.$order['id']."\n";
$pesan_wa .= '📅 *Tanggal:* '.$order['created_at']."\n";
$pesan_wa .= '🏷️ *Status:* '.$order['status']."\n";
$pesan_wa .= "------------------------------------\n";
$pesan_wa .= "*Rincian Layanan:*\n";
// Menambahkan setiap rincian item ke dalam teks pesan WhatsApp
foreach ($items as $item) {
    $pesan_wa .= '• '.$item['service_name'].' ('.$item['qty_or_weight'].' '.$item['unit_type'].') : '.format_rupiah($item['subtotal'])."\n";
}
$pesan_wa .= "------------------------------------\n";
$pesan_wa .= '💰 *Total Biaya:* *'.format_rupiah($order['total_price'])."*\n\n";
$pesan_wa .= 'Pakaian Anda segera kami proses dengan bersih, wangi, dan rapi. Terima kasih! 🙏✨';

// Melakukan encode url teks pesan agar dapat disematkan ke tautan api whatsapp
$link_wa = 'https://api.whatsapp.com/send?phone='.$no_wa.'&text='.urlencode($pesan_wa);
?>
<!DOCTYPE html>
<!-- Menentukan dokumen HTML5 -->
<html lang="id">
<!-- Membuka tag head -->
<head>
    <!-- Pengaturan karakter dokumen UTF-8 -->
    <meta charset="UTF-8">
    <!-- Pengaturan skala viewport agar responsif di layar mobile dan cetak -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul tab halaman nota pesanan -->
    <title>Nota Pemesanan #<?= $order['id']; ?> - Laundry Express</title>
    
    <!-- Memuat stylesheet Bootstrap 5 dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat ikon Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Memuat Google Fonts Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Gaya CSS kustom untuk tampilan nota dan mode cetak struk -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }
        /* Mengatur bentuk kartu nota seperti kuitansi profesional */
        .nota-container {
            max-width: 750px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        /* Header bagian atas nota */
        .nota-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 100%);
            color: white;
            padding: 30px;
        }
        /* Pengaturan tabel rincian item */
        .table-nota th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        /* Pengaturan cetak kertas nota (CSS Print Mode) */
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .nota-container {
                box-shadow: none;
                border: 1px solid #000;
                margin: 0;
                max-width: 100%;
                border-radius: 0;
            }
            .nota-header {
                background: #0d6efd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
<!-- Menutup tag head -->
</head>
<!-- Membuka tag body -->
<body>

    <!-- Kontainer Pembungkus Nota -->
    <div class="container pb-5">
        
        <!-- Baris Tombol Aksi Navigasi (Sembunyi saat dicetak) -->
        <div class="d-flex justify-content-between align-items-center max-w-750 mx-auto mt-4 mb-3 no-print" style="max-width: 750px;">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
            <div class="d-flex gap-2">
                <!-- Tombol Kirim Rincian Nota via WhatsApp -->
                <a href="<?= $link_wa; ?>" target="_blank" class="btn btn-success rounded-pill px-3 fw-semibold shadow-sm">
                    <i class="fa-brands fa-whatsapp me-1"></i> Kirim WhatsApp
                </a>
                <!-- Tombol Cetak Dokumen Nota -->
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-3 fw-semibold shadow-sm">
                    <i class="fa-solid fa-print me-1"></i> Cetak Struk
                </button>
            </div>
        </div>

        <!-- Kartu Tampilan Nota Transaksi -->
        <div class="nota-container">
            <!-- Header Kartu Nota -->
            <div class="nota-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-1"><i class="fa-solid fa-soap me-2"></i>LAUNDRY EXPRESS</h4>
                        <p class="mb-0 opacity-75 small">Layanan Cuci Bersih, Kering & Setrika Kilat</p>
                    </div>
                    <div class="text-md-end">
                        <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill fs-6 mb-1">
                            NOTA #<?= $order['id']; ?>
                        </span>
                        <div class="small opacity-75"><?= $order['created_at']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Isi Informasi Pelanggan dan Pesanan -->
            <div class="p-4">
                <div class="row g-3 mb-4">
                    <!-- Kolom Informasi Pelanggan -->
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold">Informasi Pelanggan:</span>
                        <h5 class="fw-bold text-dark mt-1 mb-1"><?= htmlspecialchars($order['customer_name']); ?></h5>
                        <p class="text-secondary small mb-0">
                            <i class="fa-brands fa-whatsapp text-success me-1"></i> <?= htmlspecialchars($order['whatsapp_number']); ?>
                        </p>
                    </div>
                    <!-- Kolom Status dan Waktu Transaksi -->
                    <div class="col-sm-6 text-sm-end">
                        <span class="text-muted small text-uppercase fw-bold">Status Pengerjaan:</span>
                        <div class="mt-1">
                            <?php
                                $status_badge = 'bg-warning text-dark';
if ($order['status'] === 'Dicuci') {
    $status_badge = 'bg-info text-white';
}
if ($order['status'] === 'Selesai') {
    $status_badge = 'bg-success text-white';
}
?>
                            <span class="badge <?= $status_badge; ?> px-3 py-2 rounded-pill fs-6 fw-bold">
                                <?= $order['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tabel Rincian Paket Layanan yang Dipesan -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-nota align-middle">
                        <thead>
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
                            <?php $no = 1;
foreach ($items as $item) { ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($item['service_name']); ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border rounded-pill">
                                        <?= htmlspecialchars($item['category']); ?>
                                    </span>
                                </td>
                                <td class="text-end text-muted">
                                    <?= format_rupiah($item['price_per_unit']); ?> / <?= $item['unit_type']; ?>
                                </td>
                                <td class="text-center fw-bold">
                                    <?= $item['qty_or_weight']; ?> <?= $item['unit_type']; ?>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    <?= format_rupiah($item['subtotal']); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <!-- Baris Total Akhir Biaya Cucian -->
                            <tr class="table-light">
                                <td colspan="5" class="text-end fw-bold py-3 fs-5">TOTAL BIAYA:</td>
                                <td class="text-end fw-bold text-primary py-3 fs-5">
                                    <?= format_rupiah($order['total_price']); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pesan Penutup & Ketentuan Pengambilan -->
                <div class="p-3 bg-light rounded-3 border small text-muted">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-exclamation text-primary me-1"></i> Catatan & Ketentuan Laundry:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Pengambilan cucian wajib menunjukkan struk / nota ini atau konfirmasi nomor WhatsApp.</li>
                        <li>Estimasi pengerjaan reguler selesai dalam 2-3 hari, express selesai dalam 3 jam.</li>
                        <li>Barang yang tidak diambil lebih dari 30 hari di luar tanggung jawab pihak laundry.</li>
                    </ul>
                </div>

                <!-- Bagian Tanda Tangan Kasir dan Pelanggan untuk Struk Fisik -->
                <div class="row mt-4 pt-3 text-center">
                    <div class="col-6">
                        <div class="small text-muted mb-5">Hormat Kami, Petugas Kasir</div>
                        <div class="fw-bold text-dark">( .................................. )</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-5">Pelanggan Yang Menyerahkan</div>
                        <div class="fw-bold text-dark">( <?= htmlspecialchars($order['customer_name']); ?> )</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memuat Javascript Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Menutup tag body dan html -->
</body>
</html>
