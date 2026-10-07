<?php
// Menentukan judul halaman untuk ditampilkan pada navigasi header
$judul_halaman = 'Kelola Tarif & Layanan';

// Memasukkan template header admin yang sudah mencakup koneksi dan pengecekan sesi login
require_once __DIR__.'/header.php';

// Menyiapkan variabel penampung pesan feedback untuk pengguna
$pesan = '';
$tipe_pesan = '';

// -------------------------------------------------------------
// OPERASI CREATE & UPDATE: Pemrosesan Form Tambah / Edit Layanan
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_layanan'])) {
    // Mengambil ID layanan jika dalam mode edit (jika kosong berarti mode tambah baru)
    $id_layanan = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    // Mengambil dan membersihkan input nama paket layanan
    $service_name = bersihkan_input($koneksi, $_POST['service_name']);
    // Mengambil dan membersihkan kategori layanan
    $category = bersihkan_input($koneksi, $_POST['category']);
    // Mengambil tarif harga dan memastikan tipe numerik integer positif
    $price_per_unit = max(0, (int) $_POST['price_per_unit']);
    // Mengambil dan membersihkan jenis satuan (Kg atau Pcs)
    $unit_type = bersihkan_input($koneksi, $_POST['unit_type']);
    // Mengambil dan membersihkan teks deskripsi layanan
    $description = bersihkan_input($koneksi, $_POST['description']);

    // Memvalidasi apakah input nama dan harga sudah terisi
    if (empty($service_name) || $price_per_unit <= 0) {
        $pesan = 'Nama layanan dan tarif harga wajib diisi dengan benar!';
        $tipe_pesan = 'danger';
    } else {
        if ($id_layanan > 0) {
            // OPERASI UPDATE: Mengubah data paket layanan yang sudah ada berdasarkan ID
            $query_update = "UPDATE services SET 
                                service_name = '$service_name', 
                                category = '$category', 
                                price_per_unit = $price_per_unit, 
                                unit_type = '$unit_type', 
                                description = '$description' 
                             WHERE id = $id_layanan";
            if (mysqli_query($koneksi, $query_update)) {
                $pesan = 'Paket layanan berhasil diperbarui!';
                $tipe_pesan = 'success';
            } else {
                $pesan = 'Gagal memperbarui data: '.mysqli_error($koneksi);
                $tipe_pesan = 'danger';
            }
        } else {
            // OPERASI CREATE: Menambahkan paket layanan baru ke tabel services
            $query_insert = "INSERT INTO services (service_name, category, price_per_unit, unit_type, description) 
                             VALUES ('$service_name', '$category', $price_per_unit, '$unit_type', '$description')";
            if (mysqli_query($koneksi, $query_insert)) {
                $pesan = 'Paket layanan baru berhasil ditambahkan!';
                $tipe_pesan = 'success';
            } else {
                $pesan = 'Gagal menambahkan layanan: '.mysqli_error($koneksi);
                $tipe_pesan = 'danger';
            }
        }
    }
}

// -------------------------------------------------------------
// OPERASI DELETE: Menghapus Data Layanan Berdasarkan ID
// -------------------------------------------------------------
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = (int) $_GET['id'];
    // Menjalankan query DELETE untuk menghapus layanan dari tabel services
    $query_hapus = "DELETE FROM services WHERE id = $id_hapus";
    if (mysqli_query($koneksi, $query_hapus)) {
        $pesan = 'Paket layanan berhasil dihapus dari sistem!';
        $tipe_pesan = 'success';
    } else {
        $pesan = 'Gagal menghapus layanan: '.mysqli_error($koneksi);
        $tipe_pesan = 'danger';
    }
}

// Menyiapkan variabel data layanan yang akan diedit jika parameter URL edit tersedia
$data_edit = null;
if (isset($_GET['aksi']) && $_GET['aksi'] === 'edit' && isset($_GET['id'])) {
    $id_edit = (int) $_GET['id'];
    $q_edit = mysqli_query($koneksi, "SELECT * FROM services WHERE id = $id_edit");
    $data_edit = mysqli_fetch_assoc($q_edit);
}

// Mengambil seluruh daftar layanan dari database untuk ditampilkan pada tabel master
$daftar_layanan = mysqli_query($koneksi, 'SELECT * FROM services ORDER BY id DESC');
?>

<!-- Header Halaman Kelola Layanan -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Tarif & Paket Layanan</h3>
        <p class="text-muted small mb-0">Kelola master data layanan cuci kiloan, satuan, dan express (CRUD) sesuai ketentuan modul.</p>
    </div>
    <!-- Tombol Trigger Modal Tambah Layanan Baru -->
    <button type="button" class="btn btn-primary rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahLayanan">
        <i class="fa-solid fa-plus-circle me-1"></i> Tambah Layanan Baru
    </button>
</div>

<!-- Menampilkan notifikasi alert jika ada pesan sukses atau error -->
<?php if (! empty($pesan)) { ?>
<div class="alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i><?= $pesan; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>

<!-- Formulir Edit Langsung Jika Parameter Edit Dipilih -->
<?php if ($data_edit) { ?>
<div class="card border-primary border-opacity-25 rounded-4 p-4 mb-4 shadow-sm bg-primary bg-opacity-10">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Paket Layanan: <?= htmlspecialchars($data_edit['service_name']); ?></h5>
        <a href="layanan.php" class="btn btn-sm btn-outline-secondary rounded-pill">Batal Edit</a>
    </div>
    <form method="POST" action="layanan.php">
        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Nama Layanan / Paket</label>
                <input type="text" name="service_name" class="form-control" value="<?= htmlspecialchars($data_edit['service_name']); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Kategori Layanan</label>
                <select name="category" class="form-select" required>
                    <option value="Kiloan" <?= ($data_edit['category'] === 'Kiloan') ? 'selected' : ''; ?>>Kiloan</option>
                    <option value="Satuan" <?= ($data_edit['category'] === 'Satuan') ? 'selected' : ''; ?>>Satuan</option>
                    <option value="Express" <?= ($data_edit['category'] === 'Express') ? 'selected' : ''; ?>>Express</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Jenis Satuan</label>
                <select name="unit_type" class="form-select" required>
                    <option value="Kg" <?= ($data_edit['unit_type'] === 'Kg') ? 'selected' : ''; ?>>Kg (Kilogram)</option>
                    <option value="Pcs" <?= ($data_edit['unit_type'] === 'Pcs') ? 'selected' : ''; ?>>Pcs (Satuan)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Tarif Harga (Rp)</label>
                <input type="number" name="price_per_unit" class="form-control" value="<?= $data_edit['price_per_unit']; ?>" min="0" required>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-bold">Deskripsi Layanan</label>
                <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($data_edit['description']); ?>">
            </div>
        </div>
        <div class="mt-3 text-end">
            <button type="submit" name="simpan_layanan" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
<?php } ?>

<!-- Tabel Daftar Master Layanan -->
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
                <?php if (mysqli_num_rows($daftar_layanan) > 0) { ?>
                    <?php while ($srv = mysqli_fetch_assoc($daftar_layanan)) { ?>
                    <tr>
                        <td class="fw-bold text-muted"><?= $srv['id']; ?></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($srv['service_name']); ?></td>
                        <td>
                            <?php
                                $badge_c = 'bg-info bg-opacity-10 text-info';
                        if ($srv['category'] === 'Satuan') {
                            $badge_c = 'bg-warning bg-opacity-10 text-warning';
                        }
                        if ($srv['category'] === 'Express') {
                            $badge_c = 'bg-danger bg-opacity-10 text-danger';
                        }
                        ?>
                            <span class="badge <?= $badge_c; ?> rounded-pill px-3 py-1 fw-bold">
                                <?= $srv['category']; ?>
                            </span>
                        </td>
                        <td class="fw-bold text-primary"><?= format_rupiah($srv['price_per_unit']); ?></td>
                        <td>
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">
                                Per <?= $srv['unit_type']; ?>
                            </span>
                        </td>
                        <td class="small text-muted" style="max-width: 250px;"><?= htmlspecialchars($srv['description']); ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- Tombol Edit Layanan -->
                                <a href="layanan.php?aksi=edit&id=<?= $srv['id']; ?>" class="btn btn-outline-warning" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <!-- Tombol Hapus Layanan dengan Konfirmasi JavaScript -->
                                <a href="layanan.php?aksi=hapus&id=<?= $srv['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus paket layanan ini?')" class="btn btn-outline-danger" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada paket layanan. Silakan klik tombol 'Tambah Layanan Baru'.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Layanan Baru -->
<div class="modal fade" id="modalTambahLayanan" tabindex="-1" aria-labelledby="modalTambahLayananLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahLayananLabel">
                    <i class="fa-solid fa-circle-plus text-primary me-2"></i>Tambah Layanan Laundry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Form Input Data Tambah Layanan Baru -->
            <form method="POST" action="layanan.php">
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
                    <button type="submit" name="simpan_layanan" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Memasukkan template footer admin
require_once __DIR__.'/footer.php';
?>
