# 🧺 Laundry Express - Sistem Pendaftaran Layanan Laundry & Cuci Kilat

Aplikasi web pemesanan jasa cuci & setrika yang mencatat transaksi berat pakaian (Kg), layanan satuan, serta paket express / dry cleaning kilat. Dibuat menggunakan framework **Laravel** dengan arsitektur MVC (Model-View-Controller) yang bersih, rapi, dan mudah dipelajari oleh developer junior maupun pelajar kejuruan/mahasiswa.

---

## 🎯 Tujuan & Fungsi Aplikasi

Aplikasi ini dibuat berdasarkan spesifikasi modul tugas praktikum **"Sistem Pendaftaran Layanan Laundry / Cuci Kilat"**, dengan tujuan:
1. **Untuk Pelanggan (Customer):**
   - Melihat katalog paket dan tarif laundry (kiloan, satuan, dan express).
   - Melakukan pendaftaran pemesanan secara mandiri dengan memasukkan nama, nomor WhatsApp, serta estimasi berat/jumlah pakaian.
   - Menghitung perkiraan total biaya secara otomatis dan *real-time*.
   - Mendapatkan bukti nota/struk pemesanan digital yang dapat langsung dikirim via WhatsApp atau dicetak fisik.
   - Melacak progres cucian secara *real-time* (Antrean &rarr; Dicuci &rarr; Selesai).

2. **Untuk Administrator (Kasir/Pengelola):**
   - Mengautentikasi login aman menggunakan sistem sesi Laravel Auth.
   - Melihat ringkasan pendapatan, total pesanan, dan status cucian di Dashboard.
   - Mengelola master tarif & paket layanan (CRUD: Tambah, Lihat, Edit, Hapus).
   - Memantau transaksi dan memperbarui progres pengerjaan cuci (`Antrean` &rarr; `Dicuci` &rarr; `Selesai`).

---

## 🗄️ Struktur Basis Data (`db_laundry_express`)

Sesuai ketentuan dalam modul, aplikasi menggunakan 3 tabel utama transaksi ditambah tabel autentikasi:

| Tabel | Keterangan | Kolom-Kolom Utama |
|---|---|---|
| **`services`** | Master Layanan | `id`, `service_name` (Varchar), `category` (Enum: Kiloan, Satuan, Express), `price_per_unit` (Integer), `unit_type` (Enum: Kg, Pcs), `description` (Text), `timestamps` |
| **`orders`** | Header Transaksi | `id`, `customer_name` (Varchar), `whatsapp_number` (Varchar), `total_price` (Integer), `status` (Enum: Antrean, Dicuci, Selesai), `timestamps` |
| **`order_details`** | Detail Transaksi | `id`, `order_id` (FK ke orders), `service_id` (FK ke services), `qty_or_weight` (Integer), `subtotal` (Integer), `timestamps` |
| **`users`** | Akun Admin | `id`, `name`, `username` (Unique), `email`, `password`, `timestamps` |

---

## 🚀 Panduan Menjalankan Aplikasi

### Opsi A: Menggunakan Script Otomatis (Windows)
Jika baru saja melakukan clone repo ini, cukup jalankan:
```cmd
setup.bat
```
Script tersebut akan otomatis menyalin `.env.example` ke `.env`, menginstall dependensi composer, membuat application key, dan menjalankan database migration + seeder!

### Opsi B: Langkah Manual
1. **Clone repositori:**
   ```bash
   git clone https://github.com/lordbarry21/laundry-express-laravel.git
   cd laundry-express-laravel
   ```
2. **Install dependensi:**
   ```bash
   composer install
   ```
3. **Konfigurasi Environment (`.env`):**
   Salin dari contoh:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan port MySQL Anda di `.env` (default XAMPP: 3306 atau 3307):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3307
   DB_DATABASE=db_laundry_express
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. **Generate App Key & Migrasi Data:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka peramban di: **`http://127.0.0.1:8000`**

---

## 🔄 Cara Push Perubahan ke Git (Kapan Saja)

File `.env`, `vendor/`, `node_modules/`, dan cache lokal sudah otomatis masuk `.gitignore`. Setiap kali Anda mengubah kode dan ingin push ke GitHub, cukup jalankan perintah standar ini:

```bash
git add .
git commit -m "Update fitur atau tampilan baru"
git push
```
Tanpa perlu mengubah pengaturan apapun lagi.

---

## 🔑 Akun Login Administrator

- **URL Login:** `http://127.0.0.1:8000/login`
- **Username:** `admin`
- **Password:** `admin123`

---

## 📂 Struktur Direktori Proyek

```
Tugas Budinda Laundry/
├── app/
│   ├── Http/Controllers/
│   │   ├── OrderController.php             # Halaman pelanggan, form pesan & tracking
│   │   ├── AuthController.php              # Login & logout admin
│   │   └── Admin/
│   │       ├── DashboardController.php     # Statistik ringkasan pesanan & omzet
│   │       ├── ServiceController.php       # CRUD Master tarif paket layanan
│   │       └── OrderAdminController.php    # Manajemen pesanan & update status cuci
│   └── Models/
│       ├── Service.php                     # Model tabel services
│       ├── Order.php                       # Model tabel orders
│       ├── OrderDetail.php                 # Model tabel order_details
│       └── User.php                        # Model tabel users (Admin)
├── database/
│   ├── migrations/                         # Berkas migrasi skema tabel
│   └── seeders/DatabaseSeeder.php          # Seeder akun admin & paket layanan awal
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                   # Layout publik pelanggan
│   │   └── admin.blade.php                 # Layout khusus dashboard admin
│   ├── customer/
│   │   ├── index.blade.php                 # Beranda, formulir pesanan & pelacakan
│   │   └── nota.blade.php                  # Bukti nota kuitansi & cetak struk
│   ├── auth/
│   │   └── login.blade.php                 # Form login administrator
│   └── admin/
│       ├── dashboard.blade.php             # Tampilan dashboard admin
│       ├── services/index.blade.php        # Halaman CRUD tarif & paket cuci
│       └── orders/index.blade.php          # Halaman filter & pengubah status cuci
├── routes/web.php                          # Definisi rute web aplikasi
├── native_php_version/                     # Cadangan arsip versi PHP Native sebelumnya
├── DOKUMENTASI_LENGKAP.md                  # Panduan langkah demi langkah dari awal s.d. akhir
└── README.md                               # Berkas penjelasan ini
```

---

## 💡 Fitur Unggulan
1. **Dynamic Dynamic Row Input:** Pelanggan dapat memesan lebih dari satu layanan sekaligus dalam 1 nota.
2. **Kalkulasi Real-Time:** Subtotal dan total biaya dihitung otomatis menggunakan JavaScript di sisi browser sebelum dikirim.
3. **Integrasi WhatsApp:** Nota memiliki tombol sekali klik untuk mengirim konfirmasi langsung ke nomor WhatsApp pelanggan.
4. **Mode Cetak Bersih (`@media print`):** Tombol dan elemen navigasi otomatis disembunyikan saat kuitansi dicetak ke kertas.
5. **Komentar Penjelasan Tiap Baris:** Seluruh kode diberi keterangan berbahasa Indonesia agar mudah dijelaskan saat presentasi di depan guru / penguji.
