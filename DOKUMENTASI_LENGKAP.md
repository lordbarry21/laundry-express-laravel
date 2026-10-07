# 📖 DOKUMENTASI LENGKAP: PENGEMBANGAN SISTEM LAUNDRY EXPRESS DENGAN LARAVEL

Dokumentasi ini menjelaskan langkah demi langkah (dari awal instalasi hingga pengujian akhir) pembuatan sistem informasi web **Pendaftaran Layanan Laundry / Cuci Kilat** menggunakan framework **Laravel** bergaya human junior developer, lengkap dengan penjelasan fungsi tiap bagian kodenya.

---

## 📑 DAFTAR ISI
1. [Latar Belakang & Spesifikasi Modul](#1-latar-belakang--spesifikasi-modul)
2. [Langkah 1: Setup Proyek & Konfigurasi Lingkungan](#2-langkah-1-setup-proyek--konfigurasi-lingkungan)
3. [Langkah 2: Migrasi Basis Data (Database Migrations)](#3-langkah-2-migrasi-basis-data-database-migrations)
4. [Langkah 3: Pembuatan Model Eloquent & Relasi Data](#4-langkah-3-pembuatan-model-eloquent--relasi-data)
5. [Langkah 4: Data Awal & Seeder (DatabaseSeeder)](#5-langkah-4-data-awal--seeder-databaseseeder)
6. [Langkah 5: Pembuatan Controller (Logika Aplikasi)](#6-langkah-5-pembuatan-controller-logika-aplikasi)
7. [Langkah 6: Konfigurasi Perutean (routes/web.php)](#7-langkah-6-konfigurasi-perutean-routeswebphp)
8. [Langkah 7: Antarmuka Pengguna (Blade Views & Layout)](#8-langkah-7-antarmuka-pengguna-blade-views--layout)
9. [Langkah 8: Cara Menjalankan & Pengujian Alur Kerja (Workflow)](#9-langkah-8-cara-menjalankan--pengujian-alur-kerja-workflow)

---

## 1. Latar Belakang & Spesifikasi Modul

Berdasarkan modul **"Sistem Pendaftaran Layanan Laundry / Cuci Kilat"**, aplikasi dirancang untuk:
- Mencatat transaksi cucian berbasis berat pakaian (Kg), satuan, maupun layanan express dry cleaning.
- **Nama Basis Data:** `db_laundry_express`
- **Tabel Sesuai Modul:**
  1. `services` (Master Layanan): `id`, `service_name`, `category` (Kiloan, Satuan, Express), `price_per_unit`, `unit_type` (Kg, Pcs), `description`, `timestamps`.
  2. `orders` (Header Transaksi): `id`, `customer_name`, `whatsapp_number`, `total_price`, `status` (Antrean, Dicuci, Selesai), `timestamps`.
  3. `order_details` (Detail Transaksi): `id`, `order_id` (FK), `service_id` (FK), `qty_or_weight`, `subtotal`, `timestamps`.
  4. `users` (Autentikasi Admin): `id`, `name`, `username`, `email`, `password`, `timestamps`.
- **Alur Kerja (Workflow):**
  - **Pelanggan:** Memilih paket layanan, menginput perkiraan berat/unit dan nomor WhatsApp, melakukan pemesanan, melihat nota, dan melacak progres status.
  - **Admin:** Login, mengelola tarif & paket layanan (CRUD), serta mengupdate progres pengerjaan cuci (`Antrean` &rarr; `Dicuci` &rarr; `Selesai`).

---

## 2. Langkah 1: Setup Proyek & Konfigurasi Lingkungan

### 1.1. Inisialisasi Proyek Laravel
Di command prompt atau PowerShell, proyek diinisialisasi menggunakan Composer:
```bash
composer create-project laravel/laravel .
```

### 1.2. Konfigurasi File Lingkungan (`.env`)
Buka file `.env` dan atur koneksi basis data MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307                  # Port MySQL (3307 atau 3306 sesuai port XAMPP Anda)
DB_DATABASE=db_laundry_express
DB_USERNAME=root
DB_PASSWORD=
```
*Penjelasan:* Mengarahkan framework Laravel ke server database lokal MySQL dengan database bernama `db_laundry_express` tanpa password default XAMPP.

---

## 3. Langkah 2: Migrasi Basis Data (Database Migrations)

File migrasi digunakan untuk membuat tabel basis data secara terstruktur melalui kode PHP.

### 2.1. Menyesuaikan Tabel Pengguna (`0001_01_01_000000_create_users_table.php`)
Menambahkan kolom `username` agar admin dapat login menggunakan username (`admin`):
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                                    // Primary Key auto-increment
    $table->string('name');                          // Nama lengkap admin
    $table->string('username')->unique();            // Username unik untuk login
    $table->string('email')->unique();               // Email unik
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');                      // Password terenkripsi
    $table->rememberToken();
    $table->timestamps();                            // created_at & updated_at
});
```

### 2.2. Migrasi Tabel Master Layanan (`2026_10_07_000001_create_services_table.php`)
```php
Schema::create('services', function (Blueprint $table) {
    $table->id();                                                   // ID unik layanan
    $table->string('service_name', 100);                            // Nama paket layanan cuci
    $table->enum('category', ['Kiloan', 'Satuan', 'Express']);      // Kategori paket
    $table->integer('price_per_unit');                              // Tarif harga per Kg/Pcs
    $table->enum('unit_type', ['Kg', 'Pcs']);                       // Jenis satuan ukuran
    $table->text('description')->nullable();                         // Keterangan rincian paket
    $table->timestamps();                                           // Catatan waktu pembuatan/update
});
```

### 2.3. Migrasi Tabel Header Transaksi (`2026_10_07_000002_create_orders_table.php`)
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();                                                                 // No. Nota Transaksi
    $table->string('customer_name', 100);                                         // Nama pemesan
    $table->string('whatsapp_number', 20);                                        // Kontak WhatsApp
    $table->integer('total_price')->default(0);                                   // Akumulasi total biaya
    $table->enum('status', ['Antrean', 'Dicuci', 'Selesai'])->default('Antrean'); // Status progres cuci
    $table->timestamps();                                                         // Waktu transaksi
});
```

### 2.4. Migrasi Tabel Detail Transaksi (`2026_10_07_000003_create_order_details_table.php`)
```php
Schema::create('order_details', function (Blueprint $table) {
    $table->id();                                                              // ID baris rincian
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');     // Relasi FK ke orders
    $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // Relasi FK ke services
    $table->integer('qty_or_weight');                                          // Jumlah berat atau unit
    $table->integer('subtotal');                                               // Subtotal harga item
    $table->timestamps();
});
```

---

## 4. Langkah 3: Pembuatan Model Eloquent & Relasi Data

Eloquent ORM memetakan tabel basis data menjadi objek PHP yang mudah dimanipulasi.

### 4.1. Model `App\Models\Service.php`
```php
class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = ['service_name', 'category', 'price_per_unit', 'unit_type', 'description'];

    // Relasi One-to-Many: 1 Layanan dapat muncul pada banyak order details
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'service_id');
    }
}
```

### 4.2. Model `App\Models\Order.php`
```php
class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['customer_name', 'whatsapp_number', 'total_price', 'status'];

    // Relasi One-to-Many: 1 Order memiliki banyak rincian di order details
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
```

### 4.3. Model `App\Models\OrderDetail.php`
```php
class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $fillable = ['order_id', 'service_id', 'qty_or_weight', 'subtotal'];

    // Relasi Many-to-One: Mengacu kembali ke 1 header order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relasi Many-to-One: Mengacu ke 1 master layanan
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
```

---

## 5. Langkah 4: Data Awal & Seeder (`DatabaseSeeder.php`)

Seeder berfungsi mengisikan data awal secara otomatis agar sistem langsung siap digunakan untuk demonstrasi:
1. Akun Admin: `username: admin`, `password: admin123`.
2. Paket Layanan:
   - Cuci Komplit Reguler (Kiloan, Rp 7.000 / Kg)
   - Cuci Kering Lipat Cepat (Kiloan, Rp 5.000 / Kg)
   - Cuci Kilat Express 3 Jam (Express, Rp 15.000 / Kg)
   - Bed Cover King Size (Satuan, Rp 25.000 / Pcs)
   - Jas / Blazer Dry Cleaning (Satuan, Rp 35.000 / Pcs)
   - Selimut Tebal Bulu Halus (Satuan, Rp 20.000 / Pcs)
3. Contoh transaksi awal (Siti Rahmawati dan Budi Santoso).

Eksekusi perintah seeder:
```bash
php artisan migrate --seed
```

---

## 6. Langkah 5: Pembuatan Controller (Logika Aplikasi)

Mengikuti pola human junior developer: kode ditulis terstruktur, lugas, dan memiliki validasi yang jelas.

1. **`OrderController.php` (Pelanggan):**
   - `index()`: Mengambil daftar layanan dan memproses pencarian pelacakan pesanan via nomor WhatsApp atau ID nota.
   - `store()`: Memvalidasi input nama pelanggan, no WA, dan array layanan. Menghitung subtotal & total belanja secara dinamis di dalam Database Transaction, lalu mengarahkan ke halaman nota.
   - `show()`: Memuat relasi pesanan dan rincian item layanan untuk dicetak / dikirim ke WhatsApp.

2. **`AuthController.php` (Login & Logout):**
   - `loginForm()`: Menampilkan form login.
   - `login()`: Mengautentikasi kredensial menggunakan `Auth::attempt()`.
   - `logout()`: Menghapus sesi menggunakan `Auth::logout()` dan `session()->invalidate()`.

3. **`Admin\DashboardController.php`:**
   - Menghitung statistik: Total Pesanan, Total Pendapatan (`sum('total_price')`), pesanan antrean, dicuci, dan selesai.

4. **`Admin\ServiceController.php` (CRUD Master Layanan):**
   - `index()`: Menampilkan daftar layanan.
   - `store()`: Menyimpan paket layanan baru dari modal.
   - `update()`: Memperbarui nama, tarif, atau jenis satuan.
   - `destroy()`: Menghapus paket layanan.

5. **`Admin\OrderAdminController.php` (Manajemen Pesanan & Status Cuci):**
   - `index()`: Menampilkan pesanan dengan fitur filter status dan pencarian keyword.
   - `updateStatus()`: Mengubah status cuci (`Antrean` &rarr; `Dicuci` &rarr; `Selesai`).
   - `destroy()`: Menghapus data transaksi.

---

## 7. Langkah 6: Konfigurasi Perutean (`routes/web.php`)

Rute dipisahkan secara rapi antara rute publik pelanggan dan rute terproteksi admin:
```php
// Rute Publik (Pelanggan)
Route::get('/', [OrderController::class, 'index'])->name('home');
Route::post('/pesan', [OrderController::class, 'store'])->name('orders.store');
Route::get('/nota/{order}', [OrderController::class, 'show'])->name('orders.show');

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Khusus Admin (Dilindungi middleware 'auth')
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/layanan', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/layanan/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/layanan/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/pesanan', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::patch('/pesanan/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/pesanan/{order}', [OrderAdminController::class, 'destroy'])->name('orders.destroy');
});
```

---

## 8. Langkah 7: Antarmuka Pengguna (Blade Views & Layout)

Semua halaman menggunakan Blade Templating Engine dengan Bootstrap 5 dan Font Awesome:
1. **`layouts/app.blade.php`**: Layout umum untuk sisi pelanggan.
2. **`layouts/admin.blade.php`**: Layout dasbor admin dengan navbar aktif dan indikator akun.
3. **`customer/index.blade.php`**:
   - Menampilkan Hero section & kartu katalog layanan.
   - Formulir pendaftaran cuci dengan JavaScript kalkulasi otomatis: ketika pelanggan memilih layanan dan menginput jumlah/berat, subtotal dan total harga langsung terhitung tanpa reload halaman.
   - Fitur Lacak Status Cucian (Tracking) lengkap dengan progress bar animasi.
4. **`customer/nota.blade.php`**:
   - Desain kuitansi elegan dengan rincian biaya lengkap.
   - Tombol **Kirim WhatsApp**: secara otomatis membuka link chat WhatsApp dengan teks format kuitansi rapi.
   - Tombol **Cetak Struk**: memicu cetak jendela browser (`window.print()`) dengan CSS print mode bersih tanpa tombol navbar.
5. **`auth/login.blade.php`**: Form login administrator dengan proteksi CSRF.
6. **`admin/dashboard.blade.php`**: Kartu metrik pendapatan, status cucian, dan tabel transaksi terkini.
7. **`admin/services/index.blade.php`**: Tabel CRUD layanan dengan modal tambah dan edit.
8. **`admin/orders/index.blade.php`**: Pengelola status cucian interaktif (dropdown otomatis submit saat diubah).

---

## 9. Langkah 8: Cara Menjalankan & Pengujian Alur Kerja (Workflow)

### 9.1. Menjalankan Server
```bash
php artisan serve
```
Akses di browser: `http://127.0.0.1:8000`

### 9.2. Pengujian Alur Kerja (Workflow Sesuai Modul):
1. **Skenario Pelanggan Mendaftar:**
   - Buka `http://127.0.0.1:8000`.
   - Lihat daftar paket (misal: Cuci Komplit Reguler Rp 7.000 / Kg).
   - Isi Nama: *Bari Hidayat*, No. WhatsApp: *081234567890*.
   - Pilih paket dan masukkan perkiraan berat: *5 Kg*.
   - Amati total estimasi berubah otomatis menjadi *Rp 35.000*.
   - Klik **"Konfirmasi & Kirim Pemesanan Sekarang"**.
   - Halaman otomatis berpindah ke Nota Transaksi.
   - Klik **"Kirim WhatsApp"** atau **"Cetak Struk"**.

2. **Skenario Pelanggan Melacak Cucian:**
   - Kembali ke halaman beranda di bagian **Lacak Status Cucian**.
   - Masukkan nomor nota atau nomor WhatsApp.
   - Klik **"Cek Status"** &rarr; sistem menampilkan status terkini (`Antrean`, `Dicuci`, atau `Selesai`) beserta progress bar.

3. **Skenario Admin Mengelola Layanan & Update Progres:**
   - Masuk ke `http://127.0.0.1:8000/login`.
   - Masukkan username: `admin` dan password: `admin123`.
   - Dasbor menampilkan total omzet dan jumlah antrean.
   - Masuk ke menu **"Kelola Tarif & Layanan"** &rarr; Admin dapat menambah tarif baru atau mengedit harga.
   - Masuk ke menu **"Transaksi & Progres Cuci"** &rarr; Admin mengubah status pesanan dari `Antrean` menjadi `Dicuci` atau `Selesai`.
   - Status di sisi pelanggan akan otomatis berubah saat dilacak ulang!
