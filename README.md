# 🧺 Laundry Express - Sistem Pendaftaran Layanan Laundry & Cuci Kilat

Aplikasi web pemesanan jasa cuci & setrika yang mencatat transaksi berat pakaian (Kg), layanan satuan (Pcs), serta paket express / dry cleaning kilat. Dibuat menggunakan framework **Laravel**, **XAMPP (MySQL)**, dan **Node.js (Vite & Tailwind CSS)** dengan **UI murni bawaan resmi Laravel Breeze (Blade Stack)** yang bersih, sederhana, dan mudah dipelajari.

---

## 🎯 Tujuan & Pemisahan Halaman (Sesuai Kebutuhan Belajar)

Aplikasi ini memisahkan setiap fungsionalitas ke dalam halaman tersendiri agar alur sistem sangat jelas dan mudah dipahami:
1. **Halaman User / Pelanggan Tersendiri (`/`):**
   - Menampilkan formulir pendaftaran pemesanan cuci kilat.
   - Menampilkan katalog tarif paket laundry (Kiloan, Satuan, dan Express).
   - Menghitung perkiraan total biaya secara *real-time* sebelum submit.
   - Menghasilkan bukti nota pendaftaran pesanan (`/order/{id}/success`) dengan nomor antrean dan tombol cetak struk / konfirmasi WhatsApp.
2. **Halaman Dashboard Admin Tersendiri (`/dashboard`):**
   - Menampilkan kartu ringkasan transaksi (Total Pesanan, Antrean, Sedang Dicuci, Selesai, Omzet).
   - Filter status pesanan (Semua, Antrean, Dicuci, Selesai).
   - Tabel transaksi pesanan lengkap dengan fitur update progres cuci langsung (`Antrean` &rarr; `Dicuci` &rarr; `Selesai`) dan hapus pesanan.
3. **Halaman Master Layanan CRUD Tersendiri (`/services`):**
   - Halaman Index: Tabel daftar seluruh paket dan tarif layanan cuci.
   - Halaman Create Tersendiri (`/services/create`): Formulir penambahan paket layanan baru.
   - Halaman Edit Tersendiri (`/services/{id}/edit`): Formulir perubahan data dan tarif layanan.
   - Tombol Hapus: Menghapus layanan dengan dialog konfirmasi.

---

## 🗄️ Struktur Basis Data (`db_laundry_express`)

| Tabel | Keterangan | Kolom-Kolom Utama |
|---|---|---|
| **`services`** | Master Layanan | `id`, `service_name` (Varchar), `category` (Enum: Kiloan, Satuan, Express), `price_per_unit` (Integer), `unit_type` (Enum: Kg, Pcs), `description` (Text), `timestamps` |
| **`orders`** | Header Transaksi | `id`, `customer_name` (Varchar), `whatsapp_number` (Varchar), `total_price` (Integer), `status` (Enum: Antrean, Dicuci, Selesai), `timestamps` |
| **`order_details`** | Detail Transaksi | `id`, `order_id` (FK ke orders), `service_id` (FK ke services), `qty_or_weight` (Integer), `subtotal` (Integer), `timestamps` |
| **`users`** | Akun Admin | `id`, `name`, `username`, `email`, `password`, `timestamps` |

---

## 🔑 Akun Login Administrator

- **URL Login:** `http://127.0.0.1:8000/login`
- **Email:** `admin@laundry.com`
- **Password:** `password123`

---

## 🚀 Panduan Menjalankan Aplikasi

1. **Jalankan Apache & MySQL di XAMPP:**
   - Buka XAMPP Control Panel, pastikan MySQL dan Apache dalam kondisi *Running*.
   - Port default di `.env` disesuaikan dengan port MySQL XAMPP Anda (misal `3306` atau `3307`).

2. **Setup Database & Data Awal:**
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Kompilasi Frontend (Node.js & Vite):**
   ```bash
   npm install
   npm run build
   ```

4. **Jalankan Server Lokal Laravel:**
   ```bash
   php artisan serve
   ```
   Aplikasi aktif di: **`http://127.0.0.1:8000`**

5. **Jalankan Pengujian Otomatis (PHPUnit Feature Tests):**
   ```bash
   php artisan test --filter=LaundryTest
   ```

---

## 📖 Dokumentasi Lengkap Per Fase

Untuk dokumentasi teknis yang mencakup seluruh baris perintah terminal, seluruh kode file yang dibuat, dan penjelasan tiap baris kode dari awal hingga akhir, silakan buka file:
👉 **`DOKUMENTASI_LENGKAP.md`**
