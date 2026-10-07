<?php
// Menentukan judul halaman admin untuk disematkan di header
$judul_halaman = 'Dashboard Ringkasan';

// Memasukkan file template header admin
require_once __DIR__.'/header.php';

// Menghitung total keseluruhan transaksi pesanan dari tabel orders
$q_total_order = mysqli_query($koneksi, 'SELECT COUNT(*) as total FROM orders');
$total_order = mysqli_fetch_assoc($q_total_order)['total'];

// Menghitung total akumulasi pendapatan kotor dari pesanan yang masuk
$q_pendapatan = mysqli_query($koneksi, 'SELECT SUM(total_price) as total FROM orders');
$total_pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

// Menghitung jumlah pesanan yang masih berstatus 'Antrean'
$q_antrean = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM orders WHERE status = 'Antrean'");
$total_antrean = mysqli_fetch_assoc($q_antrean)['total'];

// Menghitung jumlah pesanan yang sedang dalam proses 'Dicuci'
$q_dicuci = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM orders WHERE status = 'Dicuci'");
$total_dicuci = mysqli_fetch_assoc($q_dicuci)['total'];

// Menghitung jumlah pesanan yang pengerjaannya sudah 'Selesai'
$q_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM orders WHERE status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($q_selesai)['total'];

// Menghitung jumlah total ragam paket layanan yang tersedia di tabel services
$q_layanan = mysqli_query($koneksi, 'SELECT COUNT(*) as total FROM services');
$total_layanan = mysqli_fetch_assoc($q_layanan)['total'];

// Mengambil 5 data pesanan terbaru untuk ditampilkan pada tabel ringkasan dashboard
$pesanan_terbaru = mysqli_query($koneksi, 'SELECT * FROM orders ORDER BY id DESC LIMIT 5');
?>

<!-- Header Sambutan Dashboard -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Pengelola Laundry</h3>
        <p class="text-muted small mb-0">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['admin_fullname']); ?></strong>. Pantau transaksi dan status pengerjaan cuci.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="layanan.php" class="btn btn-primary rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Tambah Layanan
        </a>
        <a href="pesanan.php" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-list me-1"></i> Semua Pesanan
        </a>
    </div>
</div>

<!-- Kartu Statistik Indikator Kinerja Utama -->
<div class="row g-3 mb-4">
    <!-- Kartu 1: Total Pendapatan -->
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Total Pendapatan</span>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded-3"><i class="fa-solid fa-money-bill-wave fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= format_rupiah($total_pendapatan); ?></h4>
            <span class="text-success small fw-semibold"><i class="fa-solid fa-arrow-trend-up me-1"></i>Dari <?= $total_order; ?> transaksi</span>
        </div>
    </div>
    <!-- Kartu 2: Pesanan Dalam Antrean -->
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Antrean Masuk</span>
                <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3"><i class="fa-solid fa-clock fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= $total_antrean; ?> Pesanan</h4>
            <span class="text-warning small fw-semibold"><i class="fa-solid fa-hourglass-start me-1"></i>Menunggu dicuci</span>
        </div>
    </div>
    <!-- Kartu 3: Sedang Dicuci -->
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Sedang Dicuci</span>
                <div class="p-2 bg-info bg-opacity-10 text-info rounded-3"><i class="fa-solid fa-jug-detergent fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= $total_dicuci; ?> Pesanan</h4>
            <span class="text-info small fw-semibold"><i class="fa-solid fa-spinner fa-spin me-1"></i>Dalam pengerjaan</span>
        </div>
    </div>
    <!-- Kartu 4: Selesai Dikerjakan -->
    <div class="col-sm-6 col-xl-3">
        <div class="p-4 stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-bold text-uppercase">Cucian Selesai</span>
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3"><i class="fa-solid fa-circle-check fa-lg"></i></div>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= $total_selesai; ?> Pesanan</h4>
            <span class="text-primary small fw-semibold"><i class="fa-solid fa-box-check me-1"></i>Siap diambil</span>
        </div>
    </div>
</div>

<!-- Tabel Ringkasan Pesanan Terbaru -->
<div class="table-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Pesanan Masuk Terbaru</h5>
            <span class="text-muted small">5 transaksi terakhir yang didaftarkan pelanggan</span>
        </div>
        <a href="pesanan.php" class="text-decoration-none small fw-bold text-primary">Lihat Selengkapnya <i class="fa-solid fa-arrow-right ms-1"></i></a>
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
                <?php if (mysqli_num_rows($pesanan_terbaru) > 0) { ?>
                    <?php while ($ord = mysqli_fetch_assoc($pesanan_terbaru)) { ?>
                    <tr>
                        <td class="fw-bold text-primary">#<?= $ord['id']; ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($ord['customer_name']); ?></td>
                        <td>
                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $ord['whatsapp_number'])); ?>" target="_blank" class="text-success text-decoration-none small fw-semibold">
                                <i class="fa-brands fa-whatsapp me-1"></i><?= htmlspecialchars($ord['whatsapp_number']); ?>
                            </a>
                        </td>
                        <td class="fw-bold text-dark"><?= format_rupiah($ord['total_price']); ?></td>
                        <td>
                            <?php
                                $st_class = 'bg-warning text-dark';
                        if ($ord['status'] === 'Dicuci') {
                            $st_class = 'bg-info text-white';
                        }
                        if ($ord['status'] === 'Selesai') {
                            $st_class = 'bg-success text-white';
                        }
                        ?>
                            <span class="badge <?= $st_class; ?> rounded-pill px-3 py-1">
                                <?= $ord['status']; ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= $ord['created_at']; ?></td>
                        <td class="text-center">
                            <a href="../nota.php?id=<?= $ord['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Lihat Struk">
                                <i class="fa-solid fa-receipt me-1"></i> Struk
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan laundry yang masuk.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Memasukkan file template footer admin
require_once __DIR__.'/footer.php';
?>
