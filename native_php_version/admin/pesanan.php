<?php
// Menentukan judul halaman admin
$judul_halaman = 'Transaksi & Progres Cuci';

// Memasukkan template header admin yang memuat navbar dan verifikasi login
require_once __DIR__.'/header.php';

// Menyiapkan variabel notifikasi untuk menampilkan status eksekusi perintah
$pesan = '';
$tipe_pesan = '';

// -------------------------------------------------------------
// OPERASI UPDATE STATUS: Mengubah Progres Pengerjaan Cucian
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Mengambil dan memastikan ID order berupa bilangan integer
    $order_id = (int) $_POST['order_id'];
    // Mengambil nilai status baru ('Antrean', 'Dicuci', atau 'Selesai')
    $status_baru = bersihkan_input($koneksi, $_POST['status']);

    // Validasi opsi status agar sesuai dengan tipe data ENUM tabel orders
    if (in_array($status_baru, ['Antrean', 'Dicuci', 'Selesai'])) {
        // Query UPDATE untuk memperbarui status pengerjaan pada tabel orders
        $query_update = "UPDATE orders SET status = '$status_baru' WHERE id = $order_id";
        if (mysqli_query($koneksi, $query_update)) {
            $pesan = "Status pengerjaan pesanan #$order_id berhasil diubah menjadi: $status_baru";
            $tipe_pesan = 'success';
        } else {
            $pesan = 'Gagal memperbarui status: '.mysqli_error($koneksi);
            $tipe_pesan = 'danger';
        }
    }
}

// -------------------------------------------------------------
// OPERASI DELETE ORDER: Menghapus Data Pesanan
// -------------------------------------------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = (int) $_GET['id'];
    // Query DELETE untuk menghapus order (relasi order_details akan terhapus otomatis via ON DELETE CASCADE)
    $query_hapus = "DELETE FROM orders WHERE id = $id_hapus";
    if (mysqli_query($koneksi, $query_hapus)) {
        $pesan = "Pesanan #$id_hapus berhasil dihapus!";
        $tipe_pesan = 'success';
    } else {
        $pesan = 'Gagal menghapus pesanan: '.mysqli_error($koneksi);
        $tipe_pesan = 'danger';
    }
}

// Menyiapkan filter status dari parameter URL GET
$filter_status = isset($_GET['status']) ? bersihkan_input($koneksi, $_GET['status']) : '';
// Menyiapkan kata kunci pencarian jika ada
$keyword = isset($_GET['q']) ? bersihkan_input($koneksi, $_GET['q']) : '';

// Menyusun klausa WHERE dinamis berdasarkan filter dan pencarian
$where_clauses = [];
if (! empty($filter_status) && in_array($filter_status, ['Antrean', 'Dicuci', 'Selesai'])) {
    $where_clauses[] = "status = '$filter_status'";
}
if (! empty($keyword)) {
    $where_clauses[] = "(id = '$keyword' OR customer_name LIKE '%$keyword%' OR whatsapp_number LIKE '%$keyword%')";
}

// Menggabungkan kondisi filter
$sql_where = count($where_clauses) > 0 ? 'WHERE '.implode(' AND ', $where_clauses) : '';

// Menjalankan query pengambilan data orders dengan pengurutan terbaru di atas
$query_orders = mysqli_query($koneksi, "SELECT * FROM orders $sql_where ORDER BY id DESC");
?>

<!-- Header Halaman Transaksi -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Transaksi & Progres Cuci</h3>
        <p class="text-muted small mb-0">Pantau pesanan pelanggan, rincian biaya, dan perbarui progres pengerjaan cucian secara berkala.</p>
    </div>
</div>

<!-- Menampilkan notifikasi feedback jika ada -->
<?php if (! empty($pesan)) { ?>
<div class="alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i><?= $pesan; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>

<!-- Bar Filter Kategori Status & Pencarian Transaksi -->
<div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
    <div class="row g-2 align-items-center">
        <!-- Tab Filter Status -->
        <div class="col-lg-6">
            <div class="btn-group btn-group-sm w-100 flex-wrap" role="group">
                <a href="pesanan.php" class="btn <?= empty($filter_status) ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3 m-1">
                    Semua
                </a>
                <a href="pesanan.php?status=Antrean" class="btn <?= ($filter_status === 'Antrean') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?> rounded-pill px-3 m-1">
                    <i class="fa-solid fa-clock me-1"></i> Antrean
                </a>
                <a href="pesanan.php?status=Dicuci" class="btn <?= ($filter_status === 'Dicuci') ? 'btn-info text-white' : 'btn-outline-info text-dark'; ?> rounded-pill px-3 m-1">
                    <i class="fa-solid fa-jug-detergent me-1"></i> Sedang Dicuci
                </a>
                <a href="pesanan.php?status=Selesai" class="btn <?= ($filter_status === 'Selesai') ? 'btn-success' : 'btn-outline-success'; ?> rounded-pill px-3 m-1">
                    <i class="fa-solid fa-circle-check me-1"></i> Selesai
                </a>
            </div>
        </div>
        <!-- Kolom Pencarian -->
        <div class="col-lg-6">
            <form method="GET" action="pesanan.php" class="d-flex gap-2">
                <?php if (! empty($filter_status)) { ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <?php } ?>
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari nama, No. Nota, WhatsApp..." value="<?= htmlspecialchars($keyword); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <?php if (! empty($keyword) || ! empty($filter_status)) { ?>
                <a href="pesanan.php" class="btn btn-outline-secondary" title="Reset Filter"><i class="fa-solid fa-rotate-left"></i></a>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

<!-- Tabel Daftar Transaksi Pesanan -->
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
                <?php if (mysqli_num_rows($query_orders) > 0) { ?>
                    <?php while ($ord = mysqli_fetch_assoc($query_orders)) { ?>
                    <?php
                        // Mengambil rincian layanan detail untuk order ini
                        $ord_id = $ord['id'];
                        $q_det = mysqli_query($koneksi, "
                            SELECT od.*, s.service_name, s.unit_type 
                            FROM order_details od 
                            JOIN services s ON od.service_id = s.id 
                            WHERE od.order_id = $ord_id
                        ");
                        ?>
                    <tr>
                        <!-- Nomor Nota Pesanan -->
                        <td class="fw-bold text-primary">#<?= $ord['id']; ?></td>
                        
                        <!-- Nama Lengkap Pelanggan -->
                        <td class="fw-semibold text-dark">
                            <?= htmlspecialchars($ord['customer_name']); ?>
                            <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($ord['created_at'])); ?></div>
                        </td>

                        <!-- Nomor Kontak WhatsApp -->
                        <td>
                            <?php
                                    $wa_num = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $ord['whatsapp_number']));
                        $wa_chat = 'Halo '.$ord['customer_name'].', info update cucian Anda (Nota #'.$ord['id'].') saat ini berstatus: '.$ord['status'].'.';
                        ?>
                            <a href="https://wa.me/<?= $wa_num; ?>?text=<?= urlencode($wa_chat); ?>" target="_blank" class="text-success text-decoration-none small fw-semibold">
                                <i class="fa-brands fa-whatsapp me-1"></i><?= htmlspecialchars($ord['whatsapp_number']); ?>
                            </a>
                        </td>

                        <!-- Ringkasan Rincian Item Cucian -->
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                <?php while ($item = mysqli_fetch_assoc($q_det)) { ?>
                                    <li>
                                        <i class="fa-solid fa-angle-right text-muted me-1"></i>
                                        <?= htmlspecialchars($item['service_name']); ?>
                                        <span class="badge bg-light text-dark border ms-1"><?= $item['qty_or_weight']; ?> <?= $item['unit_type']; ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </td>

                        <!-- Total Biaya Cucian -->
                        <td class="fw-bold text-dark fs-6">
                            <?= format_rupiah($ord['total_price']); ?>
                        </td>

                        <!-- Lencana Status Cuci Saat Ini -->
                        <td>
                            <?php
                            $badge_s = 'bg-warning text-dark';
                        if ($ord['status'] === 'Dicuci') {
                            $badge_s = 'bg-info text-white';
                        }
                        if ($ord['status'] === 'Selesai') {
                            $badge_s = 'bg-success text-white';
                        }
                        ?>
                            <span class="badge <?= $badge_s; ?> rounded-pill px-3 py-2 fw-bold">
                                <?= $ord['status']; ?>
                            </span>
                        </td>

                        <!-- Form Cepat Update Progres Status Cuci -->
                        <td>
                            <form method="POST" action="pesanan.php" class="d-flex gap-1 align-items-center">
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="order_id" value="<?= $ord['id']; ?>">
                                <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                    <option value="Antrean" <?= ($ord['status'] === 'Antrean') ? 'selected' : ''; ?>>Antrean</option>
                                    <option value="Dicuci" <?= ($ord['status'] === 'Dicuci') ? 'selected' : ''; ?>>Dicuci</option>
                                    <option value="Selesai" <?= ($ord['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </form>
                        </td>

                        <!-- Tombol Aksi: Cetak Struk dan Hapus -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="../nota.php?id=<?= $ord['id']; ?>" target="_blank" class="btn btn-outline-primary" title="Cetak Nota">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                                <a href="pesanan.php?aksi=hapus&id=<?= $ord['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" class="btn btn-outline-danger" title="Hapus Transaksi">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Data pesanan tidak ditemukan.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Memasukkan template footer admin
require_once __DIR__.'/footer.php';
?>
