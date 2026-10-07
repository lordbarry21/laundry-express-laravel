<?php
// Memasukkan file koneksi.php agar halaman ini dapat terhubung ke database db_laundry_express
require_once 'koneksi.php';

// Menyiapkan variabel untuk menampung pesan notifikasi (sukses atau error)
$pesan_notifikasi = '';
// Menyiapkan variabel penanda tipe notifikasi ('success' atau 'danger')
$tipe_notifikasi = '';

// Memeriksa apakah form pemesanan laundry disubmit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_pesan'])) {
    // Mengambil dan membersihkan input nama pelanggan dari form
    $customer_name = bersihkan_input($koneksi, $_POST['customer_name']);
    // Mengambil dan membersihkan input nomor WhatsApp pelanggan dari form
    $whatsapp_number = bersihkan_input($koneksi, $_POST['whatsapp_number']);
    // Mengambil array service_id dari layanan yang dipilih oleh pelanggan
    $service_ids = isset($_POST['service_id']) ? $_POST['service_id'] : [];
    // Mengambil array qty_or_weight (jumlah berat/unit) yang dimasukkan pelanggan
    $quantities = isset($_POST['qty_or_weight']) ? $_POST['qty_or_weight'] : [];

    // Validasi apakah nama pelanggan dan nomor WhatsApp telah diisi
    if (empty($customer_name) || empty($whatsapp_number)) {
        // Menyimpan pesan gagal jika ada data diri yang belum terisi
        $pesan_notifikasi = 'Mohon lengkapi Nama Pelanggan dan Nomor WhatsApp Anda!';
        // Menandai jenis notifikasi sebagai bahaya (danger)
        $tipe_notifikasi = 'danger';
    } elseif (empty($service_ids) || count($service_ids) === 0) {
        // Validasi jika pelanggan belum memilih satupun layanan
        $pesan_notifikasi = 'Silakan pilih minimal satu paket layanan cucian!';
        // Menandai jenis notifikasi sebagai bahaya (danger)
        $tipe_notifikasi = 'danger';
    } else {
        // Menginisialisasi variabel hitung total harga pesanan mulai dari angka 0
        $total_price = 0;
        // Menyiapkan array penampung sementara untuk rincian data detail yang valid
        $items_to_insert = [];

        // Melakukan perulangan untuk memeriksa dan menghitung subtotal tiap layanan yang dipilih
        for ($i = 0; $i < count($service_ids); $i++) {
            // Mengubah id layanan ke tipe integer agar aman
            $srv_id = (int) $service_ids[$i];
            // Mengubah jumlah berat/unit ke tipe integer dan memastikan minimal nilai 1
            $qty = isset($quantities[$i]) ? max(1, (int) $quantities[$i]) : 1;

            // Mengambil tarif harga master layanan dari tabel services berdasarkan id layanan
            $query_srv = mysqli_query($koneksi, "SELECT price_per_unit FROM services WHERE id = $srv_id");
            // Mengambil satu baris data hasil query ke dalam bentuk array asosiatif
            $data_srv = mysqli_fetch_assoc($query_srv);

            // Memeriksa apakah data master layanan ditemukan di database
            if ($data_srv) {
                // Menghitung subtotal dengan mengalikan harga satuan dan jumlah kuantitas
                $subtotal = $data_srv['price_per_unit'] * $qty;
                // Menambahkan nilai subtotal ke dalam akumulasi total_price keseluruhan pesanan
                $total_price += $subtotal;
                // Menyimpan data item ini ke dalam array antrean detail
                $items_to_insert[] = [
                    'service_id' => $srv_id,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        // Memeriksa apakah terdapat item layanan yang valid untuk disimpan ke database
        if (count($items_to_insert) > 0) {
            // Menyiapkan query INSERT untuk memasukkan data header transaksi ke tabel orders
            $query_order = "INSERT INTO orders (customer_name, whatsapp_number, total_price, status) 
                            VALUES ('$customer_name', '$whatsapp_number', $total_price, 'Antrean')";
            // Menjalankan perintah query insert orders ke database MySQL
            $simpan_order = mysqli_query($koneksi, $query_order);

            // Memeriksa apakah query header pesanan berhasil dijalankan
            if ($simpan_order) {
                // Mengambil nilai id order yang baru saja berhasil di-generate secara otomatis (auto-increment)
                $order_id = mysqli_insert_id($koneksi);

                // Melakukan perulangan untuk menyimpan setiap detail layanan ke tabel order_details
                foreach ($items_to_insert as $item) {
                    // Mengambil nilai service_id dari array item
                    $s_id = $item['service_id'];
                    // Mengambil nilai kuantitas atau berat dari array item
                    $s_qty = $item['qty'];
                    // Mengambil nilai subtotal dari array item
                    $s_subtotal = $item['subtotal'];

                    // Menyiapkan query INSERT untuk menyimpan rincian transaksi ke tabel order_details
                    $query_detail = "INSERT INTO order_details (order_id, service_id, qty_or_weight, subtotal) 
                                     VALUES ($order_id, $s_id, $s_qty, $s_subtotal)";
                    // Menjalankan query insert detail transaksi ke database
                    mysqli_query($koneksi, $query_detail);
                }

                // Mengalihkan (redirect) halaman pengguna secara otomatis ke halaman nota bukti pemesanan
                header('Location: nota.php?id='.$order_id.'&status=sukses');
                // Menghentikan eksekusi script setelah melakukan pengalihan lokasi
                exit;
            } else {
                // Jika query penyimpanan header order gagal, simpan pesan error
                $pesan_notifikasi = 'Gagal memproses pesanan: '.mysqli_error($koneksi);
                // Memberikan tipe warna merah (danger) pada notifikasi
                $tipe_notifikasi = 'danger';
            }
        }
    }
}

// Menjalankan query untuk mengambil seluruh data master layanan dari tabel services untuk ditampilkan di katalog & form
$query_layanan = mysqli_query($koneksi, 'SELECT * FROM services ORDER BY category ASC, id ASC');
// Menyiapkan array kosong untuk menampung seluruh baris master layanan
$daftar_layanan = [];
// Melakukan perulangan pengambilan baris data dari hasil query tabel services
while ($row = mysqli_fetch_assoc($query_layanan)) {
    // Menambahkan tiap baris data layanan ke dalam array $daftar_layanan
    $daftar_layanan[] = $row;
}

// Menyiapkan variabel untuk menampung hasil pelacakan pesanan jika pelanggan mencari status cuciannya
$hasil_tracking = null;
// Memeriksa apakah form pencarian tracking status diakses dengan parameter GET 'cari_tracking'
if (isset($_GET['cari_tracking']) && ! empty($_GET['keyword_tracking'])) {
    // Membersihkan kata kunci pencarian (bisa berupa ID Nota atau Nomor WhatsApp)
    $keyword = bersihkan_input($koneksi, $_GET['keyword_tracking']);
    // Menjalankan query pencarian pesanan berdasarkan nomor WhatsApp atau ID transaksi
    $query_cek = "SELECT * FROM orders WHERE id = '$keyword' OR whatsapp_number LIKE '%$keyword%' ORDER BY id DESC";
    // Mengeksekusi query pencarian status order
    $hasil_tracking = mysqli_query($koneksi, $query_cek);
}
?>
<!DOCTYPE html>
<!-- Menentukan tipe dokumen HTML5 -->
<html lang="id">
<!-- Membuka tag kepala dokumen HTML -->
<head>
    <!-- Mengatur pengkodean karakter dokumen menjadi UTF-8 -->
    <meta charset="UTF-8">
    <!-- Mengatur viewport responsif agar tampilan proporsional di perangkat ponsel dan desktop -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Menentukan judul tab browser halaman web -->
    <title>Laundry Express - Pendaftaran & Pemesanan Cuci Kilat</title>
    
    <!-- Memuat stylesheet Bootstrap 5 dari CDN untuk tata letak modern dan komponen UI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat ikon Font Awesome 6 dari CDN untuk simbol visual menarik -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Memuat Google Fonts Plus Jakarta Sans untuk tipografi profesional -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Membuka blok kustom CSS untuk mempercantik antarmuka -->
    <style>
        /* Mengatur variabel warna tema laundry modern dan elegan */
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a58ca;
            --accent-cyan: #0dcaf0;
            --bg-light: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        /* Mengatur jenis font default seluruh dokumen */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
        }
        /* Mengatur gaya navigasi bar atas */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        }
        /* Mengatur gaya gradien pada bagian banner Hero */
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0284c7 50%, #06b6d4 100%);
            color: white;
            padding: 80px 0 60px 0;
            border-radius: 0 0 40px 40px;
        }
        /* Mengatur kartu layanan dan form dengan efek shadow halus */
        .custom-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        /* Efek hover melayang ketika kursor diarahkan ke kartu layanan */
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -10px rgba(13, 110, 253, 0.15);
        }
        /* Mengatur label lencana (badge) kategori */
        .badge-kategori {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
        }
        /* Mengatur warna lencana untuk kategori Kiloan */
        .badge-kiloan { background-color: #e0f2fe; color: #0284c7; }
        /* Mengatur warna lencana untuk kategori Satuan */
        .badge-satuan { background-color: #fef3c7; color: #d97706; }
        /* Mengatur warna lencana untuk kategori Express */
        .badge-express { background-color: #fee2e2; color: #dc2626; }
        /* Mengatur status pengerjaan pesanan */
        .status-antrean { background-color: #fef3c7; color: #b45309; }
        .status-dicuci { background-color: #e0f2fe; color: #0369a1; }
        .status-selesai { background-color: #dcfce7; color: #15803d; }
    </style>
<!-- Menutup tag head -->
</head>
<!-- Membuka tag body untuk konten utama -->
<body>

    <!-- NAVBAR: Menu Navigasi Atas -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top py-3">
        <!-- Membungkus navigasi dengan kontainer bootstrap yang rapi -->
        <div class="container">
            <!-- Brand Logo dan Nama Sistem Laundry Express -->
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="index.php">
                <i class="fa-solid fa-soap fa-lg text-primary"></i>
                <span class="fs-4 tracking-tight">Laundry<span class="text-dark">Express</span></span>
            </a>
            <!-- Tombol Toggle Menu Responsif untuk Layar Handphone -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Konten Menu Navigasi yang dapat di-collapse -->
            <div class="collapse navbar-collapse" id="navMenu">
                <!-- Daftar link navigasi di sisi kanan -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-2">
                    <li class="nav-item"><a class="nav-link fw-semibold px-3 active" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#layanan">Pilihan Layanan</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#pesan">Pesan Cuci</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#tracking">Lacak Status</a></li>
                    <li class="nav-item ms-lg-2">
                        <!-- Tombol Akses Khusus Panel Administrator -->
                        <a class="btn btn-outline-primary btn-sm px-3 py-2 rounded-pill fw-semibold" href="login.php">
                            <i class="fa-solid fa-user-shield me-1"></i> Panel Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION: Bagian Banner Utama Pengenalan Sistem -->
    <header class="hero-section" id="beranda">
        <!-- Membungkus isi banner ke dalam kontainer berjarak rapi -->
        <div class="container">
            <!-- Baris flex untuk membagi teks pengantar dan gambar ilustrasi -->
            <div class="row align-items-center gy-4">
                <!-- Kolom teks sisi kiri -->
                <div class="col-lg-7">
                    <!-- Label badge keunggulan kilat -->
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold mb-3 shadow-sm">
                        <i class="fa-solid fa-bolt text-warning me-1"></i> Layanan Cepat, Bersih, Wangi & Rapi
                    </span>
                    <!-- Judul Utama Halaman Banner -->
                    <h1 class="display-5 fw-bold mb-3 text-white">Sistem Pendaftaran Layanan Laundry & Cuci Kilat</h1>
                    <!-- Paragraf Penjelas Ringkas Sesuai Modul -->
                    <p class="lead mb-4 text-white-50">
                        Pesan jasa cuci & setrika kiloan dengan hitungan berat pakaian (Kg), layanan satuan satuan, hingga paket express dry cleaning kilat tanpa repot antre.
                    </p>
                    <!-- Tombol Aksi Cepat (Call To Action) -->
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#pesan" class="btn btn-warning text-dark fw-bold px-4 py-3 rounded-pill shadow">
                            <i class="fa-solid fa-plus-circle me-1"></i> Pesan Cuci Sekarang
                        </a>
                        <a href="#tracking" class="btn btn-outline-light fw-bold px-4 py-3 rounded-pill">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Cek Status Cucian
                        </a>
                    </div>
                </div>
                <!-- Kolom gambar / ringkasan fitur di sisi kanan -->
                <div class="col-lg-5 text-center">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-sm text-start text-white">
                        <h5 class="fw-bold mb-3"><i class="fa-solid fa-circle-check text-warning me-2"></i>Keunggulan Laundry Kami:</h5>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-white-50">
                            <li><i class="fa-solid fa-check text-info me-2"></i><strong>Timbang Akurat:</strong> Biaya transparan per Kg atau per Pcs.</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i><strong>Layanan Express:</strong> Cucian kilat 3 jam siap pakai.</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i><strong>Notifikasi WhatsApp:</strong> Rincian nota langsung ke nomor ponsel Anda.</li>
                            <li><i class="fa-solid fa-check text-info me-2"></i><strong>Pantau Progres:</strong> Cek status Antrean, Dicuci, atau Selesai secara real-time.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Menampilkan pesan notifikasi pop-up alert jika ada pesan dari proses form -->
    <?php if (! empty($pesan_notifikasi)) { ?>
    <div class="container mt-4">
        <div class="alert alert-<?= $tipe_notifikasi; ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i><?= $pesan_notifikasi; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php } ?>

    <!-- SEKSI 1: KATALOG DAFTAR MASTER LAYANAN -->
    <section class="py-5" id="layanan">
        <div class="container">
            <!-- Judul dan subjudul bagian katalog paket cuci -->
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold text-uppercase">Pilihan Paket</span>
                <h2 class="fw-bold mt-2">Daftar Tarif & Layanan Laundry</h2>
                <p class="text-muted">Pilih paket terbaik sesuai kebutuhan berat cucian pakaian atau item khusus Anda</p>
            </div>

            <!-- Grid baris untuk menampilkan kartu layanan secara dinamis dari database -->
            <div class="row g-4">
                <?php foreach ($daftar_layanan as $srv) { ?>
                <div class="col-md-6 col-lg-4">
                    <!-- Kartu layanan interaktif -->
                    <div class="card custom-card service-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Bagian kategori dan jenis satuan -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <?php
                                    // Menentukan kelas CSS badge berdasarkan kategori layanan
                                    $badge_class = 'badge-kiloan';
                    if ($srv['category'] === 'Satuan') {
                        $badge_class = 'badge-satuan';
                    }
                    if ($srv['category'] === 'Express') {
                        $badge_class = 'badge-express';
                    }
                    ?>
                                <span class="badge badge-kategori <?= $badge_class; ?>">
                                    <i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($srv['category']); ?>
                                </span>
                                <span class="badge bg-light text-secondary rounded-pill border px-2 py-1 small">
                                    Per <?= htmlspecialchars($srv['unit_type']); ?>
                                </span>
                            </div>
                            <!-- Nama paket layanan cuci -->
                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($srv['service_name']); ?></h5>
                            <!-- Deskripsi rincian layanan -->
                            <p class="text-muted small mb-3"><?= htmlspecialchars($srv['description']); ?></p>
                        </div>
                        <div>
                            <!-- Menampilkan harga tarif layanan per unit dalam format rupiah -->
                            <div class="d-flex align-items-baseline mb-3">
                                <span class="fs-4 fw-bold text-primary"><?= format_rupiah($srv['price_per_unit']); ?></span>
                                <span class="text-muted small ms-1">/ <?= htmlspecialchars($srv['unit_type']); ?></span>
                            </div>
                            <!-- Tombol untuk memilih paket ini langsung ke form pemesanan -->
                            <a href="#pesan" onclick="pilihPaket('<?= $srv['id']; ?>', '<?= addslashes($srv['service_name']); ?>', <?= $srv['price_per_unit']; ?>, '<?= $srv['unit_type']; ?>')" class="btn btn-outline-primary w-100 rounded-pill fw-semibold">
                                <i class="fa-solid fa-cart-plus me-1"></i> Pilih Layanan Ini
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- SEKSI 2: FORM PENDAFTARAN & PEMESANAN LAYANAN (Sesuai Workflow Pelanggan) -->
    <section class="py-5 bg-white border-top border-bottom" id="pesan">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="custom-card p-4 p-md-5 border-0 shadow">
                        <!-- Judul formulir pendaftaran pemesanan -->
                        <div class="text-center mb-4">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold text-uppercase">Formulir Pemesanan</span>
                            <h2 class="fw-bold mt-2">Daftar & Pesan Layanan Laundry</h2>
                            <p class="text-muted">Isi identitas Anda dan pilih paket cucian untuk mendapatkan estimasi biaya dan nota pemesanan</p>
                        </div>

                        <!-- Membuka tag form POST untuk mengirim data pemesanan -->
                        <form method="POST" action="index.php#pesan" id="formOrder">
                            <!-- Input tersembunyi sebagai penanda aksi form order -->
                            <input type="hidden" name="aksi_pesan" value="1">

                            <!-- Bagian 1: Identitas Pelanggan -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary">
                                        <i class="fa-solid fa-user me-1 text-primary"></i> Nama Lengkap Pelanggan
                                    </label>
                                    <input type="text" name="customer_name" class="form-control form-control-lg rounded-3" placeholder="Contoh: Budi Santoso" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-secondary">
                                        <i class="fa-brands fa-whatsapp me-1 text-success"></i> Nomor WhatsApp Aktif
                                    </label>
                                    <input type="tel" name="whatsapp_number" class="form-control form-control-lg rounded-3" placeholder="Contoh: 081234567890" required>
                                    <div class="form-text small">Nomor WhatsApp digunakan untuk mengirimkan konfirmasi nota transaksi & update status.</div>
                                </div>
                            </div>

                            <hr class="my-4 text-muted">

                            <!-- Bagian 2: Pilihan Paket Layanan & Perkiraan Berat / Satuan -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-basket-shopping text-primary me-2"></i>Rincian Cucian Anda</h5>
                                <!-- Tombol tambah baris item pesanan -->
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold" onclick="tambahBarisItem()">
                                    <i class="fa-solid fa-plus me-1"></i> Tambah Layanan Lain
                                </button>
                            </div>

                            <!-- Kontainer daftar baris item layanan yang dapat ditambah secara dinamis -->
                            <div id="container-items" class="d-flex flex-column gap-3 mb-4">
                                <!-- Baris item pertama default -->
                                <div class="row g-2 align-items-end p-3 rounded-4 bg-light border item-row">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold small text-secondary">Pilih Layanan / Paket Cuci</label>
                                        <select name="service_id[]" class="form-select select-service" onchange="hitungTotal()" required>
                                            <option value="" disabled selected>-- Pilih Jenis Layanan --</option>
                                            <?php foreach ($daftar_layanan as $srv) { ?>
                                            <option value="<?= $srv['id']; ?>" data-harga="<?= $srv['price_per_unit']; ?>" data-satuan="<?= $srv['unit_type']; ?>">
                                                <?= htmlspecialchars($srv['service_name']); ?> (<?= format_rupiah($srv['price_per_unit']); ?> / <?= $srv['unit_type']; ?>)
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small text-secondary label-qty">Perkiraan Jumlah / Berat</label>
                                        <div class="input-group">
                                            <input type="number" name="qty_or_weight[]" class="form-control input-qty" value="1" min="1" oninput="hitungTotal()" required>
                                            <span class="input-group-text span-satuan">Unit</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small text-secondary">Subtotal Biaya</label>
                                        <input type="text" class="form-control bg-white input-subtotal fw-bold text-primary" value="Rp 0" readonly>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="hapusBarisItem(this)" title="Hapus Item">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian 3: Ringkasan Total Biaya Pesanan -->
                            <div class="p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-secondary fw-semibold small d-block">Estimasi Total Biaya Cucian:</span>
                                        <h3 class="fw-extrabold text-primary mb-0" id="tampilan-total">Rp 0</h3>
                                    </div>
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fa-solid fa-circle-check me-1"></i> Status Awal: Antrean
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Submit Pemesanan -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow py-3">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Konfirmasi & Kirim Pemesanan Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEKSI 3: LACAK STATUS PESANAN (Tracking Progress Cuci Pelanggan) -->
    <section class="py-5" id="tracking">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-4">
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-bold text-uppercase">Tracking Order</span>
                        <h2 class="fw-bold mt-2">Lacak Progres Status Cucian Anda</h2>
                        <p class="text-muted">Masukkan Nomor ID Nota Transaksi atau Nomor WhatsApp Anda untuk melihat progres pengerjaan</p>
                    </div>

                    <!-- Form Pencarian Status Pesanan -->
                    <div class="custom-card p-4 mb-4">
                        <form method="GET" action="index.php#tracking" class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                                    <input type="text" name="keyword_tracking" class="form-control border-start-0" placeholder="Nomor Nota (contoh: 1) atau No. WhatsApp..." value="<?= isset($_GET['keyword_tracking']) ? htmlspecialchars($_GET['keyword_tracking']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" name="cari_tracking" value="1" class="btn btn-primary w-100 fw-bold">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cek Status
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Menampilkan Hasil Pencarian Tracking Jika Ada -->
                    <?php if ($hasil_tracking !== null) { ?>
                        <?php if (mysqli_num_rows($hasil_tracking) > 0) { ?>
                            <div class="d-flex flex-column gap-3">
                                <?php while ($order = mysqli_fetch_assoc($hasil_tracking)) { ?>
                                    <div class="custom-card p-4">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                                            <div>
                                                <h5 class="fw-bold mb-1">Nota #<?= $order['id']; ?> - <?= htmlspecialchars($order['customer_name']); ?></h5>
                                                <span class="text-muted small"><i class="fa-solid fa-calendar me-1"></i><?= $order['created_at']; ?></span>
                                            </div>
                                            <!-- Menampilkan badge status cucian -->
                                            <?php
                                    $status_class = 'status-antrean';
                                    $status_icon = 'fa-clock';
                                    if ($order['status'] === 'Dicuci') {
                                        $status_class = 'status-dicuci';
                                        $status_icon = 'fa-jug-detergent';
                                    } elseif ($order['status'] === 'Selesai') {
                                        $status_class = 'status-selesai';
                                        $status_icon = 'fa-circle-check';
                                    }
                                    ?>
                                            <span class="badge <?= $status_class; ?> px-3 py-2 rounded-pill fw-bold fs-6">
                                                <i class="fa-solid <?= $status_icon; ?> me-1"></i> <?= $order['status']; ?>
                                            </span>
                                        </div>

                                        <!-- Progress Bar Visualisasi Status -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between text-muted small fw-bold mb-1">
                                                <span>1. Antrean Masuk</span>
                                                <span>2. Sedang Dicuci</span>
                                                <span>3. Selesai & Bersih</span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <?php
                                            $prog = 33;
                                    if ($order['status'] === 'Dicuci') {
                                        $prog = 66;
                                    }
                                    if ($order['status'] === 'Selesai') {
                                        $prog = 100;
                                    }
                                    ?>
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: <?= $prog; ?>%"></div>
                                            </div>
                                        </div>

                                        <!-- Informasi Total dan Tombol Lihat Rincian Struk -->
                                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                                            <div>
                                                <span class="text-muted small">Total Biaya:</span>
                                                <span class="fw-bold text-primary fs-5 ms-1"><?= format_rupiah($order['total_price']); ?></span>
                                            </div>
                                            <a href="nota.php?id=<?= $order['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                                <i class="fa-solid fa-receipt me-1"></i> Lihat Nota Lengkap
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <!-- Tampilan jika data pelacakan tidak ditemukan -->
                            <div class="custom-card p-5 text-center">
                                <i class="fa-solid fa-folder-open text-muted fa-3x mb-3"></i>
                                <h5 class="fw-bold text-dark">Data Cucian Tidak Ditemukan</h5>
                                <p class="text-muted mb-0">Pastikan ID Nota atau Nomor WhatsApp yang Anda masukkan sudah benar.</p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER: Bagian Kaki Halaman Sistem -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-1 fw-bold"><i class="fa-solid fa-soap text-primary me-2"></i>Sistem Pendaftaran Layanan Laundry Express</p>
            <p class="text-white-50 small mb-0">&copy; <?= date('Y'); ?> Tugas Praktikum Pengembangan Aplikasi Web - Modul Layanan Laundry</p>
        </div>
    </footer>

    <!-- Memuat Javascript Bootstrap Bundle dari CDN untuk interaktivitas modal dan navbar -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JAVASCRIPT: Logika Interaktif Form Pemesanan & Kalkulasi Otomatis Subtotal & Total -->
    <script>
        // Data master layanan dalam bentuk array objek JavaScript untuk kalkulasi instan di sisi klien
        const masterLayanan = <?= json_encode($daftar_layanan); ?>;

        // Fungsi untuk menambahkan baris layanan baru secara dinamis
        function tambahBarisItem() {
            // Mengambil elemen kontainer item
            const container = document.getElementById('container-items');
            // Membuat elemen baris div baru
            const div = document.createElement('div');
            // Menambahkan kelas bootstrap ke elemen div baru
            div.className = 'row g-2 align-items-end p-3 rounded-4 bg-light border item-row';
            
            // Menyusun opsi pilihan paket cuci dari data masterLayanan
            let optionsHtml = '<option value="" disabled selected>-- Pilih Jenis Layanan --</option>';
            masterLayanan.forEach(item => {
                optionsHtml += `<option value="${item.id}" data-harga="${item.price_per_unit}" data-satuan="${item.unit_type}">
                    ${item.service_name} (Rp ${parseInt(item.price_per_unit).toLocaleString('id-ID')} / ${item.unit_type})
                </option>`;
            });

            // Menyisipkan template HTML baris item ke dalam elemen div
            div.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Pilih Layanan / Paket Cuci</label>
                    <select name="service_id[]" class="form-select select-service" onchange="hitungTotal()" required>
                        ${optionsHtml}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary label-qty">Perkiraan Jumlah / Berat</label>
                    <div class="input-group">
                        <input type="number" name="qty_or_weight[]" class="form-control input-qty" value="1" min="1" oninput="hitungTotal()" required>
                        <span class="input-group-text span-satuan">Unit</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Subtotal Biaya</label>
                    <input type="text" class="form-control bg-white input-subtotal fw-bold text-primary" value="Rp 0" readonly>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="hapusBarisItem(this)" title="Hapus Item">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            // Memasukkan baris baru ke dalam kontainer
            container.appendChild(div);
        }

        // Fungsi untuk menghapus satu baris item pilihan layanan
        function hapusBarisItem(btn) {
            // Mengambil semua elemen baris yang ada di kontainer
            const semuaBaris = document.querySelectorAll('.item-row');
            // Memastikan minimal ada 1 baris yang tersisa agar form tidak kosong total
            if (semuaBaris.length > 1) {
                // Menghapus elemen baris terdekat dari tombol hapus
                btn.closest('.item-row').remove();
                // Menghitung ulang total harga setelah baris dihapus
                hitungTotal();
            } else {
                // Memberikan peringatan bahwa minimal harus ada 1 item layanan
                alert('Minimal harus memilih 1 jenis paket layanan!');
            }
        }

        // Fungsi untuk menghitung subtotal tiap baris dan total akumulasi keseluruhan harga pesanan
        function hitungTotal() {
            // Menginisialisasi variabel penampung akumulasi total
            let totalSemua = 0;
            // Mengambil seluruh baris item pesanan
            const barisItems = document.querySelectorAll('.item-row');

            // Melakukan perulangan pada setiap baris item
            barisItems.forEach(baris => {
                // Mengambil elemen dropdown pilih layanan
                const selectSrv = baris.querySelector('.select-service');
                // Mengambil elemen input kuantitas/berat
                const inputQty = baris.querySelector('.input-qty');
                // Mengambil elemen tampilan subtotal
                const inputSubtotal = baris.querySelector('.input-subtotal');
                // Mengambil elemen label teks satuan (Kg/Pcs)
                const spanSatuan = baris.querySelector('.span-satuan');

                // Mengambil opsi yang sedang dipilih oleh pengguna
                const opsiTerpilih = selectSrv.options[selectSrv.selectedIndex];

                // Memeriksa apakah opsi valid telah dipilih
                if (opsiTerpilih && opsiTerpilih.value) {
                    // Mengambil nilai tarif harga dari atribut data-harga
                    const harga = parseFloat(opsiTerpilih.getAttribute('data-harga')) || 0;
                    // Mengambil jenis satuan dari atribut data-satuan
                    const satuan = opsiTerpilih.getAttribute('data-satuan') || 'Unit';
                    // Mengambil jumlah kuantitas dari input
                    const qty = parseFloat(inputQty.value) || 0;

                    // Mengupdate tampilan teks satuan pada input group (misal: 'Kg' atau 'Pcs')
                    spanSatuan.textContent = satuan;

                    // Menghitung subtotal baris ini (harga dikalikan kuantitas)
                    const subtotal = harga * qty;
                    // Menampilkan subtotal dalam format mata uang Rupiah
                    inputSubtotal.value = 'Rp ' + subtotal.toLocaleString('id-ID');

                    // Menambahkan subtotal ke total keseluruhan
                    totalSemua += subtotal;
                } else {
                    // Jika belum ada layanan yang dipilih, reset subtotal ke Rp 0
                    inputSubtotal.value = 'Rp 0';
                    spanSatuan.textContent = 'Unit';
                }
            });

            // Menampilkan akumulasi total harga pada ringkasan pemesanan
            document.getElementById('tampilan-total').textContent = 'Rp ' + totalSemua.toLocaleString('id-ID');
        }

        // Fungsi pembantu ketika pelanggan mengklik tombol 'Pilih Layanan Ini' dari kartu katalog
        function pilihPaket(id, nama, harga, satuan) {
            // Mengambil baris pertama formulir
            const barisPertama = document.querySelector('.item-row');
            if (barisPertama) {
                // Mengatur pilihan dropdown ke id yang dipilih dari kartu katalog
                const select = barisPertama.querySelector('.select-service');
                select.value = id;
                // Menjalankan fungsi hitung total otomatis
                hitungTotal();
            }
        }
    </script>
<!-- Menutup tag body dan html -->
</body>
</html>
