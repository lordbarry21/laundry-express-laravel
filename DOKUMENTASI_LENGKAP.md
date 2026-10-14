# 📖 DOKUMENTASI LENGKAP & PANDUAN BELAJAR: SISTEM PENDAFTARAN LAYANAN LAUNDRY / CUCI KILAT EXPRESS

Dokumentasi ini disusun secara rinci dan terstruktur per fase sebagai bahan pembelajaran pembuatan aplikasi web **Sistem Pendaftaran Layanan Laundry / Cuci Kilat** menggunakan **Laravel**, **XAMPP (MySQL)**, dan **Node.js (Vite & Tailwind CSS)**. Antarmuka (UI) menggunakan **100% komponen bawaan resmi Laravel Breeze (Blade Stack)** tanpa dependensi template pihak ketiga yang rumit.

---

## 📑 DAFTAR ISI
1. [Spesifikasi Sistem & Struktur Database](#1-spesifikasi-sistem--struktur-database)
2. [Fase 1: Setup Lingkungan, Git Branch & Instalasi Laravel Breeze](#2-fase-1-setup-lingkungan-git-branch--instalasi-laravel-breeze)
3. [Fase 2: Pembuatan Skema Basis Data (Database Migrations)](#3-fase-2-pembuatan-skema-basis-data-database-migrations)
4. [Fase 3: Pembuatan Model Eloquent & Relasi Data](#4-fase-3-pembuatan-model-eloquent--relasi-data)
5. [Fase 4: Database Seeder (Akun Admin & Master Layanan Awal)](#5-fase-4-database-seeder-akun-admin--master-layanan-awal)
6. [Fase 5: Controller & Logika Bisnis Aplikasi](#6-fase-5-controller--logika-bisnis-aplikasi)
7. [Fase 6: Konfigurasi Perutean Web (routes/web.php)](#7-fase-6-konfigurasi-perutean-web-routeswebphp)
8. [Fase 7: Antarmuka Blade (UI Bawaan Laravel yang Terpisah Per Halaman)](#8-fase-7-antarmuka-blade-ui-bawaan-laravel-yang-terpisah-per-halaman)
9. [Fase 8: Pengujian Otomatis (Automated Feature Testing)](#9-fase-8-pengujian-otomatis-automated-feature-testing)
10. [Fase 9: Panduan Menjalankan Sistem Dari Nol (Langkah Praktik)](#10-fase-9-panduan-menjalankan-sistem-dari-nol-langkah-praktik)

---

## 1. Spesifikasi Sistem & Struktur Database

### 1.1 Skenario Aplikasi
Aplikasi pemesanan jasa cuci & setrika yang mencatat transaksi berat pakaian (Kg) atau layanan paket dry cleaning / satuan (Pcs).
- **Nama Basis Data:** `db_laundry_express`
- **Alur Kerja Pelanggan:** Memilih paket layanan, menginput perkiraan berat/jumlah unit dan kontak WhatsApp, mengirim pemesanan, lalu menerima nota transaksi dengan status awal `Antrean`.
- **Alur Kerja Admin:** Login ke sistem, mengelola tarif dan paket layanan cuci (CRUD lengkap: Index, Tambah, Edit, Hapus), serta memantau dan mengupdate progres pengerjaan cucian (`Antrean` &rarr; `Dicuci` &rarr; `Selesai`).

### 1.2 Struktur Tabel Basis Data
1. **`services` (Master Layanan):**
   - `id`: Bigint unsigned, Auto Increment, Primary Key
   - `service_name`: Varchar(255)
   - `category`: Enum (`'Kiloan'`, `'Satuan'`, `'Express'`)
   - `price_per_unit`: Integer (Tarif rupiah per unit)
   - `unit_type`: Enum (`'Kg'`, `'Pcs'`)
   - `description`: Text (Keterangan rincian paket)
   - `created_at` & `updated_at`: Timestamps
2. **`orders` (Header Transaksi):**
   - `id`: Bigint unsigned, Auto Increment, Primary Key
   - `customer_name`: Varchar(255) (Nama pemesan)
   - `whatsapp_number`: Varchar(20) (Kontak WhatsApp)
   - `total_price`: Integer (Total tagihan rupiah)
   - `status`: Enum (`'Antrean'`, `'Dicuci'`, `'Selesai'`)
   - `created_at` & `updated_at`: Timestamps
3. **`order_details` (Detail Transaksi):**
   - `id`: Bigint unsigned, Auto Increment, Primary Key
   - `order_id`: Foreign Key ke `orders(id)` ON DELETE CASCADE
   - `service_id`: Foreign Key ke `services(id)` ON DELETE CASCADE
   - `qty_or_weight`: Integer (Berat dalam Kg atau jumlah dalam Pcs)
   - `subtotal`: Integer (Perhitungan `qty_or_weight * price_per_unit`)
   - `created_at` & `updated_at`: Timestamps
4. **`users` (Autentikasi Admin):**
   - `id`, `name`, `username`, `email`, `password`, `timestamps`

---

## 2. Fase 1: Setup Lingkungan, Git Branch & Instalasi Laravel Breeze

### 2.1 Pembuatan Branch Baru & Pembersihan Kode Lama
Di terminal bash / command prompt:
```bash
# 1. Masuk ke folder proyek
cd "D:/Bari/Sekolah/Tugas Budinda Laundry"

# 2. Buat dan berpindah ke branch baru
git checkout -b sistem-laundry-express

# 3. Bersihkan kode lama agar rapi dan dimulai dari fresh slate
rm -rf native_php_version app/Http/Controllers/Admin app/Http/Controllers/AuthController.php \
       app/Http/Controllers/OrderController.php app/Models/Order.php app/Models/OrderDetail.php \
       app/Models/Service.php database/migrations/2026_* resources/views/admin \
       resources/views/customer resources/views/layouts resources/views/auth DOKUMENTASI_LENGKAP.md

# 4. Simpan commit awal pembersihan
git add -A
git commit -m "chore: wipe legacy codebase for fresh rebuild"
```

### 2.2 Konfigurasi Database XAMPP di File `.env`
Buka file `.env` di direktori proyek dan pastikan konfigurasi MySQL mengarah ke port XAMPP Anda (umumnya port `3306` atau `3307` jika MySQL 8.x lain terpasang di sistem):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=db_laundry_express
DB_USERNAME=root
DB_PASSWORD=
```

### 2.3 Instalasi Laravel Breeze (Blade Stack Resmi)
Laravel Breeze menyediakan autentikasi minimalis, layout Blade, serta utilitas CSS bawaan:
```bash
# Instalasi package Breeze
php "C:/ProgramData/ComposerSetup/bin/composer.phar" require laravel/breeze --dev

# Pasang scaffolding Blade dan Tailwind
php artisan breeze:install blade --no-interaction

# Kompilasi aset frontend Tailwind & JS
npm run build
```

---

## 3. Fase 2: Pembuatan Skema Basis Data (Database Migrations)

### 3.1 Perintah Terminal
```bash
php artisan make:migration create_services_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_details_table
```

### 3.2 Kode Lengkap File Migrasi

#### File 1: `database/migrations/0001_01_01_000000_create_users_table.php` (Penyesuaian Kolom)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

#### File 2: `database/migrations/2026_10_14_000001_create_services_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->enum('category', ['Kiloan', 'Satuan', 'Express']);
            $table->integer('price_per_unit');
            $table->enum('unit_type', ['Kg', 'Pcs']);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
```

#### File 3: `database/migrations/2026_10_14_000002_create_orders_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('whatsapp_number');
            $table->integer('total_price');
            $table->enum('status', ['Antrean', 'Dicuci', 'Selesai'])->default('Antrean');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

#### File 4: `database/migrations/2026_10_14_000003_create_order_details_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('qty_or_weight');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
```

### 3.3 Eksekusi Migrasi
```bash
php artisan migrate
```

---

## 4. Fase 3: Pembuatan Model Eloquent & Relasi Data

### 4.1 File: `app/Models/Service.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_name',
        'category',
        'price_per_unit',
        'unit_type',
        'description',
    ];

    /**
     * Relasi ke OrderDetail (satu layanan bisa ada di banyak detail pesanan)
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'service_id');
    }
}
```

### 4.2 File: `app/Models/Order.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_name',
        'whatsapp_number',
        'total_price',
        'status',
    ];

    /**
     * Relasi ke OrderDetail (satu pesanan memiliki banyak rincian item)
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
```

### 4.3 File: `app/Models/OrderDetail.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'service_id',
        'qty_or_weight',
        'subtotal',
    ];

    /**
     * Relasi balik ke pesanan induk (Order)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relasi ke paket layanan (Service)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
```

---

## 5. Fase 4: Database Seeder (Akun Admin & Master Layanan Awal)

### 5.1 File: `database/seeders/ServiceSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'service_name'   => 'Cuci Kering Setrika Reguler',
                'category'       => 'Kiloan',
                'price_per_unit' => 7000,
                'unit_type'      => 'Kg',
                'description'    => 'Layanan cuci bersih, kering, dan setrika rapi wangi tahan lama estimasi pengerjaan 2 hari.',
            ],
            [
                'service_name'   => 'Cuci Kilat Express 4 Jam',
                'category'       => 'Express',
                'price_per_unit' => 12000,
                'unit_type'      => 'Kg',
                'description'    => 'Layanan cuci dan setrika kilat selesai dalam 4-6 jam, pakaian siap pakai seketika.',
            ],
            [
                'service_name'   => 'Dry Cleaning Jas / Blazer',
                'category'       => 'Satuan',
                'price_per_unit' => 25000,
                'unit_type'      => 'Pcs',
                'description'    => 'Pembersihan profesional untuk setelan jas, blazer, atau pakaian formal tanpa merusak bahan.',
            ],
            [
                'service_name'   => 'Cuci Bed Cover Jumbo',
                'category'       => 'Satuan',
                'price_per_unit' => 20000,
                'unit_type'      => 'Pcs',
                'description'    => 'Pencucian khusus selimut tebal dan bed cover ukuran besar agar bersih, bebas tungau, dan harum.',
            ],
            [
                'service_name'   => 'Setrika Uap Express',
                'category'       => 'Express',
                'price_per_unit' => 8000,
                'unit_type'      => 'Kg',
                'description'    => 'Jasa setrika uap licin dan rapi untuk pakaian kering yang butuh cepat rapi siap pakai.',
            ],
        ];

        foreach ($services as $item) {
            Service::create($item);
        }
    }
}
```

### 5.2 File: `database/seeders/DatabaseSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Default untuk Login
        User::updateOrCreate(
            ['email' => 'admin@laundry.com'],
            [
                'name'     => 'Admin Laundry Express',
                'username' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Data Master Layanan
        $this->call(ServiceSeeder::class);

        // 3. Contoh Transaksi Awal untuk Bahan Belajar & Pengujian
        if (Order::count() === 0) {
            $service1 = Service::where('category', 'Kiloan')->first();
            $service2 = Service::where('category', 'Express')->first();

            if ($service1) {
                $order1 = Order::create([
                    'customer_name'   => 'Siti Rahmawati',
                    'whatsapp_number' => '081234567890',
                    'total_price'     => $service1->price_per_unit * 5,
                    'status'          => 'Dicuci',
                ]);

                OrderDetail::create([
                    'order_id'      => $order1->id,
                    'service_id'    => $service1->id,
                    'qty_or_weight' => 5,
                    'subtotal'      => $service1->price_per_unit * 5,
                ]);
            }

            if ($service2) {
                $order2 = Order::create([
                    'customer_name'   => 'Budi Santoso',
                    'whatsapp_number' => '089876543210',
                    'total_price'     => $service2->price_per_unit * 3,
                    'status'          => 'Antrean',
                ]);

                OrderDetail::create([
                    'order_id'      => $order2->id,
                    'service_id'    => $service2->id,
                    'qty_or_weight' => 3,
                    'subtotal'      => $service2->price_per_unit * 3,
                ]);
            }
        }
    }
}
```

### 5.3 Eksekusi Seeding
```bash
php artisan migrate:fresh --seed
```
*Hasil:* Database bersih, 5 paket layanan terbuat, akun admin terdaftar (`admin@laundry.com` / `password123`), dan 2 contoh transaksi pesanan masuk.

---

## 6. Fase 5: Controller & Logika Bisnis Aplikasi

### 6.1 File: `app/Http/Controllers/OrderController.php`
Mengatur alur formulir pelanggan, transaksi database, nota pesanan, dashboard admin, dan update status cuci:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * SISI PELANGGAN: Menampilkan halaman formulir pemesanan layanan laundry (Halaman User Tersendiri)
     */
    public function create()
    {
        $services = Service::all();
        return view('order.create', compact('services'));
    }

    /**
     * SISI PELANGGAN: Memproses transaksi pemesanan laundry baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'service_id'      => 'required|exists:services,id',
            'qty_or_weight'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data layanan yang dipilih
            $service = Service::findOrFail($validated['service_id']);

            // Hitung subtotal dan total harga transaksi
            $subtotal   = $service->price_per_unit * $validated['qty_or_weight'];
            $totalPrice = $subtotal;

            // 1. Simpan Header Transaksi (orders)
            $order = Order::create([
                'customer_name'   => $validated['customer_name'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'total_price'     => $totalPrice,
                'status'          => 'Antrean',
            ]);

            // 2. Simpan Detail Transaksi (order_details)
            OrderDetail::create([
                'order_id'      => $order->id,
                'service_id'    => $service->id,
                'qty_or_weight' => $validated['qty_or_weight'],
                'subtotal'      => $subtotal,
            ]);

            DB::commit();

            return redirect()->route('order.success', $order->id)
                ->with('success', 'Pendaftaran pesanan laundry berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * SISI PELANGGAN: Menampilkan bukti / nota pendaftaran pesanan laundry
     */
    public function success(Order $order)
    {
        $order->load('orderDetails.service');
        return view('order.success', compact('order'));
    }

    /**
     * SISI ADMIN: Halaman Dashboard Tersendiri untuk memantau rekap & statistik pesanan
     */
    public function dashboard(Request $request)
    {
        // Query pesanan dengan relasi detail dan layanan
        $query = Order::with('orderDetails.service')->latest();

        // Filter opsional berdasarkan status pengerjaan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Data statistik untuk ringkasan di dashboard
        $stats = [
            'total'     => Order::count(),
            'antrean'   => Order::where('status', 'Antrean')->count(),
            'dicuci'    => Order::where('status', 'Dicuci')->count(),
            'selesai'   => Order::where('status', 'Selesai')->count(),
            'pendapatan'=> Order::where('status', 'Selesai')->sum('total_price'),
        ];

        return view('dashboard', compact('orders', 'stats'));
    }

    /**
     * SISI ADMIN: Mengupdate progres status pengerjaan cuci (Antrean -> Dicuci -> Selesai)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Antrean,Dicuci,Selesai',
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Status pesanan #{$order->id} ({$order->customer_name}) berhasil diubah menjadi: {$order->status}!");
    }

    /**
     * SISI ADMIN: Menghapus data transaksi pesanan jika diperlukan
     */
    public function destroy(Order $order)
    {
        $orderId = $order->id;
        $order->delete();

        return back()->with('success', "Pesanan #{$orderId} berhasil dihapus!");
    }
}
```

### 6.2 File: `app/Http/Controllers/ServiceController.php`
Mengatur CRUD lengkap master tarif & paket layanan laundry:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Menampilkan daftar seluruh master layanan laundry (Halaman Index CRUD)
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Menampilkan halaman formulir tambah layanan baru (Halaman Create Tersendiri)
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Menyimpan data layanan baru ke basis data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name'   => 'required|string|max:255',
            'category'       => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0',
            'unit_type'      => 'required|in:Kg,Pcs',
            'description'    => 'nullable|string',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan halaman formulir edit layanan (Halaman Edit Tersendiri)
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Memperbarui data tarif dan layanan yang sudah ada
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name'   => 'required|string|max:255',
            'category'       => 'required|in:Kiloan,Satuan,Express',
            'price_per_unit' => 'required|integer|min:0',
            'unit_type'      => 'required|in:Kg,Pcs',
            'description'    => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry berhasil diperbarui!');
    }

    /**
     * Menghapus paket layanan dari basis data
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Paket layanan laundry berhasil dihapus!');
    }
}
```

---

## 7. Fase 6: Konfigurasi Perutean Web (`routes/web.php`)

```php
<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Pendaftaran Layanan Laundry Express
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE SISI PELANGGAN (PUBLIC / USER)
// ==========================================
// Halaman Form Pemesanan Laundry
Route::get('/', [OrderController::class, 'create'])->name('order.create');
// Proses Submit Transaksi Pemesanan
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
// Halaman Bukti / Nota Pendaftaran Pesanan
Route::get('/order/{order}/success', [OrderController::class, 'success'])->name('order.success');

// ==========================================
// 2. RUTE SISI ADMIN (TERPROTEKSI AUTH BREEZE)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman Dashboard Tersendiri: Rekap Pesanan & Progres Cuci
    Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::delete('/admin/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Halaman CRUD Master Layanan Tersendiri:
    // - GET  /services          -> Index (Daftar Layanan)
    // - GET  /services/create   -> Create (Halaman Tambah Tersendiri)
    // - POST /services          -> Store (Simpan)
    // - GET  /services/{id}/edit-> Edit (Halaman Edit Tersendiri)
    // - PUT  /services/{id}     -> Update (Perbarui)
    // - DELETE /services/{id}   -> Destroy (Hapus)
    Route::resource('services', ServiceController::class);

    // Profile Admin (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## 8. Fase 7: Antarmuka Blade (UI Bawaan Laravel yang Terpisah Per Halaman & Desain Fungsional Sederhana)

Antarmuka dirancang murni fungsional, rapi, dan berbasis warna bawaan framework Laravel Breeze (Tailwind CSS standard):
- **Daftar Tarif Layanan:** Disajikan dalam bentuk tabel data (`<table>`) yang rapi dan terstruktur, bukan tumpukan kartu.
- **Tanpa Header Berlebih:** Header banner atas di halaman pendaftaran dihapus agar layar langsung fokus ke formulir dan tabel layanan.
- **Tanpa Cross-Navigation:** Sisi pelanggan tidak memiliki tombol/link menuju admin login, dan sisi admin tidak memiliki tombol/link menuju form pelanggan.
- **Tanpa Elemen Dekoratif Tambahan:** Tidak ada shadow (bayangan), tidak ada hover animasi berlebihan, dan tidak ada footer/copyright yang tidak diperlukan.
- **Pemisahan Halaman:** Dibuat mandiri untuk masing-masing peran (User Order, Nota Sukses, Dashboard Admin, dan Master Layanan CRUD).

```
resources/views/
├── dashboard.blade.php           # [Halaman Dashboard Tersendiri] Rekap pesanan, filter status, update progres cuci
├── order/
│   ├── create.blade.php          # [Halaman User Tersendiri] Formulir pemesanan pelanggan & katalog layanan
│   └── success.blade.php         # [Halaman Nota Sukses] Rincian bukti pendaftaran cuci pelanggan
├── services/
│   ├── index.blade.php           # [Halaman Index CRUD] Tabel daftar tarif & paket layanan
│   ├── create.blade.php          # [Halaman Create Tersendiri] Form input penambahan layanan baru
│   └── edit.blade.php            # [Halaman Edit Tersendiri] Form pembaruan tarif & data layanan
└── layouts/
    ├── app.blade.php             # Layout admin dengan flash message & Tailwind Breeze
    └── navigation.blade.php      # Navigasi atas (Dashboard, Kelola Layanan, Link Form Pelanggan)
```

Setiap file di atas menggunakan komponen bawaan Laravel Breeze seperti `<x-app-layout>`, `<x-input-label>`, `<x-text-input>`, `<x-input-error>`, `<x-primary-button>`, dan utilitas CSS Tailwind bawaan tanpa menambah library visual pihak ketiga.

---

## 9. Fase 8: Pengujian Otomatis (Automated Feature Testing)

File pengujian dibuat di `tests/Feature/LaundryTest.php` untuk memvalidasi alur sistem dari sisi pelanggan maupun admin:
```bash
php artisan test --filter=LaundryTest
```

Hasil eksekusi:
```text
   PASS  Tests\Feature\LaundryTest
  ✓ customer can see order page and services                          0.18s  
  ✓ customer can place order                                          0.04s  
  ✓ customer can view order success note                              0.03s  
  ✓ admin can login and access dashboard                              0.08s  
  ✓ admin can update order status                                     0.03s  
  ✓ admin can manage services crud                                    0.07s  

  Tests:    6 passed (26 assertions)
  Duration: 1.02s
```

---

## 10. Fase 9: Panduan Menjalankan Sistem Dari Nol (Langkah Praktik)

Bagi rekan-rekan atau siapa pun yang ingin mempelajari atau menjalankan proyek ini di laptop masing-masing:

### Langkah 1: Jalankan XAMPP
1. Buka **XAMPP Control Panel**.
2. Klik tombol **Start** pada modul **MySQL** dan **Apache**.
3. Pastikan database `db_laundry_express` sudah dibuat di phpMyAdmin (`http://localhost/phpmyadmin`).

### Langkah 2: Setup Proyek & Database
Buka terminal (Git Bash atau PowerShell) di dalam folder proyek:
```bash
# 1. Pastikan dependency PHP terinstal
php "C:/ProgramData/ComposerSetup/bin/composer.phar" install

# 2. Pastikan file .env sudah sesuai port database Anda
# Jika belum ada file .env:
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi dan isi data awal otomatis
php artisan migrate:fresh --seed
```

### Langkah 3: Kompilasi Frontend
Pastikan Node.js sudah terpasang:
```bash
npm install
npm run build
```

### Langkah 4: Jalankan Server Lokal Laravel
```bash
php artisan serve
```
Aplikasi sekarang aktif di: **`http://127.0.0.1:8000`**

### Langkah 5: Uji Alur Kerja (Workflow)
1. **Sisi Pelanggan (Public):**
   - Buka browser ke: `http://127.0.0.1:8000/`
   - Isi Nama Pelanggan (contoh: *Budi Santoso*).
   - Isi Nomor WhatsApp (contoh: *081234567890*).
   - Pilih paket cuci (contoh: *Cuci Kilat Express 4 Jam*).
   - Masukkan perkiraan berat cucian (contoh: *3 Kg*).
   - Perhatikan kalkulasi estimasi total biaya yang otomatis terhitung.
   - Klik **Kirim Pendaftaran Pesanan**.
   - Anda akan langsung dialihkan ke halaman nota transaksi `#ID` dengan status `Antrean`.
2. **Sisi Admin:**
   - Buka: `http://127.0.0.1:8000/login`
   - Masukkan email: `admin@laundry.com` dan password: `password123`.
   - Di **Dashboard Pesanan**, pesanan Budi Santoso akan terlihat.
   - Ubah status pengerjaan dari `Antrean` &rarr; `Dicuci` &rarr; `Selesai` menggunakan dropdown dan tombol **Ubah**.
   - Buka menu **Master Layanan** di navigasi atas untuk menambah paket cucian baru di halaman **Tambah Layanan Tersendiri**, mengubah tarif di halaman **Edit Tersendiri**, atau menghapus layanan.
