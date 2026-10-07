-- ====================================================================
-- SKRIP BASIS DATA: db_laundry_express
-- Sesuai Modul: Sistem Pendaftaran Layanan Laundry / Cuci Kilat
-- Setiap baris kode memiliki penjelasan rinci untuk pembelajaran
-- ====================================================================

-- Membuat database baru jika belum pernah dibuat sebelumnya
CREATE DATABASE IF NOT EXISTS db_laundry_express;

-- Memilih database db_laundry_express untuk digunakan pada operasi tabel berikut
USE db_laundry_express;

-- ====================================================================
-- TABEL 1: services (Master Layanan)
-- Digunakan untuk menyimpan daftar jenis paket cuci dan tarif harga
-- ====================================================================

-- Menghapus tabel services lama jika ada agar struktur diperbarui bersih
DROP TABLE IF EXISTS order_details;
-- Menghapus tabel orders jika ada sebelum membuat ulang relasi
DROP TABLE IF EXISTS orders;
-- Menghapus tabel services
DROP TABLE IF EXISTS services;
-- Menghapus tabel admins jika ada
DROP TABLE IF EXISTS admins;

-- Membuat tabel master layanan (services)
CREATE TABLE services (
    -- Kolom id sebagai primary key bernilai integer yang bertambah otomatis
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom nama layanan/paket cuci dengan tipe teks maksimal 100 karakter
    service_name VARCHAR(100) NOT NULL,
    -- Kolom kategori layanan dengan pilihan: Kiloan, Satuan, atau Express
    category ENUM('Kiloan', 'Satuan', 'Express') NOT NULL,
    -- Kolom tarif harga per satuan/unit berupa bilangan bulat (Rupiah)
    price_per_unit INT NOT NULL,
    -- Kolom jenis satuan ukuran: Kg (Kilogram) atau Pcs (Potong/Helai)
    unit_type ENUM('Kg', 'Pcs') NOT NULL,
    -- Kolom rincian/penjelasan deskripsi paket layanan
    description TEXT,
    -- Kolom pencatat waktu data dibuat secara otomatis saat pertama kali diinput
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Kolom pencatat waktu data diubah secara otomatis saat data diperbarui
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ====================================================================
-- TABEL 2: orders (Header Transaksi)
-- Digunakan untuk mencatat data umum pesanan cucian pelanggan
-- ====================================================================

-- Membuat tabel header transaksi pesanan (orders)
CREATE TABLE orders (
    -- Kolom id sebagai nomor transaksi utama dan auto increment
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom nama lengkap pelanggan yang melakukan pendaftaran cuci
    customer_name VARCHAR(100) NOT NULL,
    -- Kolom nomor WhatsApp aktif pelanggan untuk konfirmasi dan notifikasi
    whatsapp_number VARCHAR(20) NOT NULL,
    -- Kolom akumulasi total biaya keseluruhan pesanan dalam satuan Rupiah
    total_price INT NOT NULL DEFAULT 0,
    -- Kolom status progres cuci dengan pilihan: Antrean, Dicuci, atau Selesai
    status ENUM('Antrean', 'Dicuci', 'Selesai') NOT NULL DEFAULT 'Antrean',
    -- Kolom pencatat waktu pesanan dibuat
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Kolom pencatat waktu saat status atau data pesanan diupdate oleh admin
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ====================================================================
-- TABEL 3: order_details (Detail Transaksi)
-- Digunakan untuk merinci setiap paket layanan yang dipilih dalam 1 pesanan
-- ====================================================================

-- Membuat tabel detail transaksi pesanan (order_details)
CREATE TABLE order_details (
    -- Kolom id detail transaksi sebagai identitas baris unik
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom order_id sebagai relasi Foreign Key ke tabel orders
    order_id INT NOT NULL,
    -- Kolom service_id sebagai relasi Foreign Key ke tabel services
    service_id INT NOT NULL,
    -- Kolom jumlah perkiraan berat (kg) atau jumlah unit pakaian (pcs)
    qty_or_weight INT NOT NULL,
    -- Kolom subtotal biaya untuk layanan ini (harga_per_unit dikali qty_or_weight)
    subtotal INT NOT NULL,
    -- Kolom pencatat waktu detail pesanan dibuat
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Kolom pencatat waktu saat detail diubah
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Menambahkan aturan relasi: order_id mengacu ke kolom id pada tabel orders
    CONSTRAINT fk_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    -- Menambahkan aturan relasi: service_id mengacu ke kolom id pada tabel services
    CONSTRAINT fk_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- ====================================================================
-- TABEL 4: admins (Autentikasi Pengelola Laundry)
-- Digunakan untuk fitur login admin agar dapat mengelola tarif dan pesanan
-- ====================================================================

-- Membuat tabel data admin sistem
CREATE TABLE admins (
    -- Kolom id admin otomatis bertambah
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Kolom username unik untuk keperluan login
    username VARCHAR(50) NOT NULL UNIQUE,
    -- Kolom password yang dienkripsi
    password VARCHAR(255) NOT NULL,
    -- Kolom nama asli petugas admin
    fullname VARCHAR(100) NOT NULL,
    -- Kolom pencatat waktu akun admin dibuat
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ====================================================================
-- DATA AWAL / SEEDER (Untuk Pengujian Awal Sistem)
-- ====================================================================

-- Memasukkan data awal daftar layanan cuci kiloan, satuan, dan express
INSERT INTO services (service_name, category, price_per_unit, unit_type, description) VALUES
('Cuci Komplit Reguler (Cuci + Setrika + Lipat)', 'Kiloan', 7000, 'Kg', 'Layanan cuci bersih wangi dan disetrika rapi, selesai dalam 2-3 hari kerja.'),
('Cuci Kering Lipat Cepat', 'Kiloan', 5000, 'Kg', 'Layanan cuci bersih dan dikeringkan rapi tanpa disetrika, selesai 1-2 hari.'),
('Cuci Kilat Express (3 Jam Selesai)', 'Express', 15000, 'Kg', 'Prioritas cuci kilat khusus untuk kebutuhan mendesak, selesai dalam hitungan 3 jam.'),
('Bed Cover Besar / King Size', 'Satuan', 25000, 'Pcs', 'Pembersihan mendalam untuk bed cover ukuran besar agar bebas debu dan tungau.'),
('Jas / Blazer Dry Cleaning', 'Satuan', 35000, 'Pcs', 'Perawatan khusus pakaian berbahan halus/jas formal menggunakan metode dry clean.'),
('Selimut Tebal Bulu Halus', 'Satuan', 20000, 'Pcs', 'Pencucian selimut tebal dengan pelembut premium menjaga kelembutan kain.');

-- Memasukkan akun admin default (username: admin, password: password123)
-- Password di-hash menggunakan SHA2(256) untuk keamanan dan kesesuaian verifikasi
INSERT INTO admins (username, password, fullname) VALUES
('admin', SHA2('admin123', 256), 'Administrator Laundry Express');

-- Memasukkan contoh transaksi pesanan awal
INSERT INTO orders (id, customer_name, whatsapp_number, total_price, status) VALUES
(1, 'Siti Rahmawati', '081234567890', 49000, 'Dicuci'),
(2, 'Budi Santoso', '089876543210', 30000, 'Antrean');

-- Memasukkan contoh detail transaksi pesanan awal
INSERT INTO order_details (order_id, service_id, qty_or_weight, subtotal) VALUES
(1, 1, 7, 49000),
(2, 3, 2, 30000);
