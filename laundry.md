# Sistem Pendaftaran Layanan Laundry / Cuci Kilat

**Skenario:** Aplikasi pemesanan jasa cuci & setrika yang mencatat transaksi berat pakaian (kg) atau layanan paket *dry cleaning*.

### A. Ketentuan Struktur Data & Basis Data (db_laundry_express)

- **services (Master Layanan):** id, service_name (Varchar), category (Enum: Kiloan, Satuan, Express), price_per_unit (Integer), unit_type (Enum: Kg, Pcs), description (Text), timestamps.
- **orders (Header Transaksi):** id, customer_name (Varchar), whatsapp_number (Varchar), total_price (Integer), status (Enum: Antrean, Dicuci, Selesai), timestamps.
- **order_details (Detail Transaksi):** id, order_id (Foreign Key), service_id (Foreign Key), qty_or_weight (Integer), subtotal (Integer), timestamps.

### B. Alur Kerja (Workflow)

1. **Pelanggan:** Memilih paket/layanan cuci, mengisi perkiraan berat/jumlah unit serta kontak WhatsApp, lalu melakukan pemesanan.
2. **Admin:** Login, mengelola tarif & paket layanan (CRUD), serta mengupdate progres status pengerjaan cuci.

---

Panduan langkah demi langkah membangun aplikasi pendaftaran layanan Laundry Express menggunakan Laravel + Breeze + Tailwind CSS + MySQL (database `db_laundry_express`, skema `services`, `orders`, `order_details`, `users.is_admin`).

Jalankan perintah CLI di terminal VS Code dari folder proyek `laundry`. Kecuali diminta mengganti satu file penuh (`mode: file`), cukup sisipkan/ganti blok kode yang ditunjuk agar struktur bawaan Laravel tetap rapi.

## Persiapan

### Buat project Laravel baru bernama laundry

<!-- step: {"id":"modul0-0-1-buat-project","module":0,"step":0,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Buka terminal (PowerShell, Command Prompt, atau Terminal bawaan Laragon) lalu masuk ke folder induk tempat kamu biasa menyimpan proyek (misalnya D:\Laragon\www atau D:\Projects).
2. Pastikan koneksi internet aktif karena Composer akan mengunduh kerangka Laravel beserta seluruh dependensi vendor.
3. Salin dan jalankan perintah ini di terminal tersebut (bukan di dalam folder proyek Laravel lain).

**Tujuannya:**
Membuat folder proyek Laravel baru bernama laundry lengkap dengan struktur direktori standar (app, database, resources, routes), file .env awal, serta APP_KEY otomatis agar proyek siap dikonfigurasi.

**Perhatian:**
Jangan jalankan perintah ini jika di folder tujuan sudah ada folder bernama laundry, karena Composer akan menolak menulis ke folder yang tidak kosong.

```sh
composer create-project laravel/laravel laundry
```

### Masuk ke folder proyek laundry

<!-- step: {"id":"modul0-0-2-masuk-project","module":0,"step":0,"part":2,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Di terminal yang sama tepat setelah composer create-project selesai tanpa error, ketik perintah ini.
2. Perhatikan prompt terminal sekarang harus berakhir di ...\laundry yang di dalamnya terdapat file artisan dan composer.json.

**Tujuannya:**
Mengarahkan direktori aktif terminal ke root proyek laundry supaya seluruh perintah Artisan, Composer, dan npm berikutnya terbaca di proyek yang benar.

**Perhatian:**
Jika nanti muncul error "Could not open input file: artisan", hampir pasti terminal masih berada di folder induk, belum masuk ke dalam folder laundry.

```sh
cd laundry
```

### Cek versi PHP, Composer, Node.js, dan npm

<!-- step: {"id":"modul0-0-3-cek-tools","module":0,"step":0,"part":3,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan keempat perintah pengecekan versi ini dari dalam folder laundry.
2. Pastikan masing-masing perintah mencetak nomor versi (proyek referensi memakai PHP 8.5, cukup pastikan PHP >= 8.2, Composer 2.x, Node >= 18/20, dan npm sudah terdeteksi).

**Tujuannya:**
Memastikan seluruh tooling runtime backend (PHP, Composer) dan frontend (Node.js, npm) sudah terdaftar di PATH sistem sebelum masuk ke instalasi Breeze dan kompilasi Tailwind CSS.

**Perhatian:**
Jika salah satu perintah tidak dikenali ("not recognized" atau "command not found"), perbaiki PATH Windows (tambahkan direktori php/composer/nodejs) dan buka ulang terminal sebelum melanjutkan.

```sh
php -v
composer -V
node -v
npm -v
```

### Buka proyek laundry di VS Code dan tautkan Folder kerja

<!-- step: {"id":"modul0-0-4-buka-vscode","module":0,"step":0,"part":4,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini dari dalam folder laundry untuk membuka editor VS Code langsung pada root proyek.
2. Di jendela VS Code yang baru terbuka, buka panel Panduan Serkom lalu klik tombol "Folder kerja" di bagian atas.
3. Pilih folder laundry (folder yang langsung berisi file artisan, bukan folder induknya), lalu buka Terminal VS Code dengan Ctrl+` untuk menjalankan langkah selanjutnya.

**Tujuannya:**
Menghubungkan editor VS Code dan panel Panduan Serkom ke folder proyek yang benar agar tombol "Buka file tujuan" dan "Pratinjau sisipkan kode" bisa membuka serta memodifikasi file secara otomatis.

**Perhatian:**
Pastikan kamu memilih folder laundry yang memuat file artisan saat menekan "Folder kerja". Jika memilih folder yang salah, fitur sisipkan kode otomatis tidak akan menemukan target file.

```sh
code .
```

## Modul 1 · Database dan model

### Buat database MySQL db_laundry_express

<!-- step: {"id":"modul1-1-1-buat-database","module":1,"step":1,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Nyalakan service MySQL/MariaDB terlebih dahulu lewat panel kontrol Laragon atau XAMPP hingga berstatus Running.
2. Jalankan perintah SQL di bawah lewat terminal, atau buat secara visual lewat phpMyAdmin (buka http://localhost/phpmyadmin -> menu New -> ketik db_laundry_express -> Create).
3. Jika MySQL lokal memakai port selain 3306 (misalnya 3307), tambahkan opsi -P 3307; jika user root memakai password, tambahkan -p.

**Tujuannya:**
Menyediakan database kosong bernama db_laundry_express di server MySQL agar tabel-tabel Laravel hasil migration punya tempat penyimpanan fisik.

**Perhatian:**
Nama database harus persis db_laundry_express sesuai .env di langkah berikutnya. Jika database lama sudah terpakai skema lain (misalnya laundry_items), gunakan database baru ini agar tabel tidak bentrok.

```sh
mysql -u root -e "CREATE DATABASE IF NOT EXISTS db_laundry_express;"
```

### Hubungkan Laravel ke database MySQL melalui .env

<!-- step: {"id":"modul1-1-2-database-env","module":1,"step":1,"part":2,"mode":"env","method":null,"target":".env"} -->
**Kerjakan di sini:**
1. Buka file .env di root proyek (klik "Buka file tujuan"), lalu gulir ke bagian konfigurasi DB_CONNECTION (sekitar baris 22 sampai 27).
2. Jika mengedit manual, hapus tanda komentar # di depan DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, dan DB_PASSWORD, lalu sesuaikan nilainya. Jika memakai tombol "Pratinjau sisipkan kode", pastikan baris DB_* yang masih diawali tanda # tidak dibiarkan terkomentar agar tidak bentrok atau ganda.
3. Ubah DB_PORT=3306 menjadi port MySQL aktif di komputermu (misalnya 3307 pada sebagian instalasi XAMPP/Laragon), biarkan DB_PASSWORD= kosong jika root tanpa password, lalu simpan file (Ctrl+S).

**Tujuannya:**
Mengubah driver database Laravel dari SQLite ke MySQL lokal serta menautkannya ke database db_laundry_express dengan kredensial yang tepat.

**Perhatian:**
Pada Laravel 11/12/13, baris DB_HOST sampai DB_PASSWORD awalnya diawali tanda # karena memakai SQLite. Jika tanda # lupa dihapus saat edit manual, konfigurasi tidak terbaca dan php artisan migrate akan gagal koneksi.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_laundry_express
DB_USERNAME=root
DB_PASSWORD=
```

### Buat model, migration, dan controller Service

<!-- step: {"id":"modul1-2-1-buat-service","module":1,"step":2,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Pastikan terminal VS Code berada di root folder laundry.
2. Jalankan perintah Artisan ini untuk menghasilkan tiga file sekaligus: Model Service, file migration create_services_table, dan ServiceController beserta method resource.

**Tujuannya:**
Menyiapkan kerangka tabel services (katalog layanan cuci/setrika), model Eloquent Service, serta controller ServiceController (-m migration, -c controller, -r resource method CRUD).

**Perhatian:**
Nama model wajib tunggal dengan huruf awal kapital (Service). Laravel secara otomatis memetakannya ke nama tabel jamak services.

```sh
php artisan make:model Service -mcr
```

### Buat model, migration, dan controller Order

<!-- step: {"id":"modul1-2-2-buat-order","module":1,"step":2,"part":2,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Di terminal VS Code folder proyek laundry, jalankan perintah ini tepat setelah pembuatan Service selesai.
2. Perintah menghasilkan Model Order, file migration create_orders_table, dan OrderController.

**Tujuannya:**
Menyiapkan entitas transaksi induk pesanan (Order) yang menyimpan identitas pelanggan, nomor WhatsApp, total harga server-side, dan progres status cucian.

**Perhatian:**
Pastikan perintah dijalankan urut (Service lalu Order lalu OrderDetail) agar timestamp file migration berurutan sebelum OrderDetail merujuk foreign key ke services dan orders.

```sh
php artisan make:model Order -mcr
```

### Buat model dan migration OrderDetail

<!-- step: {"id":"modul1-2-3-buat-orderdetail","module":1,"step":2,"part":3,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal folder laundry.
2. Cukup gunakan flag -m (hanya Model dan Migration) karena rincian item pesanan nanti dikelola langsung oleh OrderController.

**Tujuannya:**
Menyiapkan entitas OrderDetail dan migration tabel order_details yang menyimpan setiap baris layanan, qty/berat, dan subtotal di dalam satu transaksi Order.

**Perhatian:**
Gunakan penulisan PascalCase OrderDetail (tanpa spasi/underscore) supaya Laravel otomatis memetakannya ke tabel snake_case order_details.

```sh
php artisan make:model OrderDetail -m
```

### Isi kolom tabel services di file migration

<!-- step: {"id":"modul1-3-1-migration-services","module":1,"step":3,"part":1,"mode":"migration","method":null,"target":"database/migrations/xxxx_xx_xx_xxxxxx_create_services_table.php"} -->
**Kerjakan di sini:**
1. Buka file migration yang namanya berakhiran _create_services_table.php di folder database/migrations/ (tombol "Buka file tujuan" otomatis mencari file dengan pola nama tersebut).
2. Perhatikan method up() di dalam Schema::create('services', function (Blueprint $table) { ... }).
3. Klik "Pratinjau sisipkan kode" atau letakkan kelima kolom di bawah tepat di antara $table->id(); dan $table->timestamps(); lalu simpan (Ctrl+S).

**Tujuannya:**
Membentuk skema master layanan laundry: service_name (nama layanan), category enum (Kiloan/Satuan/Express), price_per_unit integer tarif per satuan, unit_type enum (Kg/Pcs), dan description opsional (nullable).

**Perhatian:**
Ejaan kolom (service_name, price_per_unit, unit_type) serta huruf kapital nilai enum (Kiloan, Satuan, Express dan Kg, Pcs) harus persis sama karena seeder, validasi controller, dan view Blade bergantung pada nama tersebut.

```php
$table->string('service_name');
$table->enum('category', ['Kiloan', 'Satuan', 'Express']);
$table->integer('price_per_unit');
$table->enum('unit_type', ['Kg', 'Pcs']);
$table->text('description')->nullable();
```

### Isi kolom tabel orders di file migration

<!-- step: {"id":"modul1-3-2-migration-orders","module":1,"step":3,"part":2,"mode":"migration","method":null,"target":"database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php"} -->
**Kerjakan di sini:**
1. Buka file database/migrations/..._create_orders_table.php.
2. Sisipkan kolom di bawah di antara $table->id(); dan $table->timestamps(); di dalam method up(), lalu simpan file.

**Tujuannya:**
Menyimpan header setiap pesanan pelanggan: nama (customer_name), nomor WhatsApp (whatsapp_number), total biaya akhir (total_price), serta status pengerjaan enum dengan nilai awal otomatis Antrean.

**Perhatian:**
Nilai default('Antrean') wajib dipasang agar setiap pesanan yang masuk dari halaman publik langsung masuk antrean tanpa perlu diinput manual.

```php
$table->string('customer_name');
$table->string('whatsapp_number');
$table->integer('total_price');
$table->enum('status', ['Antrean', 'Dicuci', 'Selesai'])->default('Antrean');
```

### Isi kolom dan relasi foreign key tabel order_details

<!-- step: {"id":"modul1-3-3-migration-order-details","module":1,"step":3,"part":3,"mode":"migration","method":null,"target":"database/migrations/xxxx_xx_xx_xxxxxx_create_order_details_table.php"} -->
**Kerjakan di sini:**
1. Buka file database/migrations/..._create_order_details_table.php.
2. Masukkan dua kolom foreignId dan dua kolom angka ini di antara $table->id(); dan $table->timestamps(); di dalam method up(), lalu simpan.

**Tujuannya:**
Membuat tabel detail pesanan yang menautkan order_id -> orders.id dan service_id -> services.id (dengan onDelete cascade), disertai qty_or_weight (jumlah Kg/Pcs bilangan bulat) dan subtotal tarif item tersebut.

**Perhatian:**
Pastikan kedua parent tabel (orders dan services) memiliki timestamp migration yang lebih lama daripada order_details, jika tidak MySQL akan menolak foreign key constraint saat migrate.

```php
$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
$table->foreignId('service_id')->constrained('services')->onDelete('cascade');
$table->integer('qty_or_weight');
$table->integer('subtotal');
```

### Jalankan migration awal ke MySQL

<!-- step: {"id":"modul1-4-1-migrate","module":1,"step":4,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Pastikan MySQL berjalan, database db_laundry_express sudah dibuat, .env sudah disimpan, dan ketiga file migration di atas sudah disimpan.
2. Jalankan perintah ini di terminal VS Code pada folder proyek laundry.

**Tujuannya:**
Menerapkan semua migration bawaan Laravel (users, cache, jobs, sessions) dan ketiga tabel aplikasi (services, orders, order_details) beserta constraint foreign key ke database MySQL.

**Perhatian:**
Gunakan php artisan migrate biasa, jangan biasakan memakai migrate:fresh saat database sudah menyimpan data penting. Jika gagal koneksi, periksa ulang DB_PORT dan status MySQL aktif.

```sh
php artisan migrate
```

### Verifikasi semua migration berstatus Ran

<!-- step: {"id":"modul1-4-2-migrate-status","module":1,"step":4,"part":2,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah pengecekan status ini di terminal tepat setelah php artisan migrate selesai.
2. Pastikan setiap file migration di tabel output termasuk create_services_table, create_orders_table, dan create_order_details_table menampilkan status Ran (bukan Pending).

**Tujuannya:**
Memverifikasi dari terminal bahwa seluruh tabel inti sudah benar-benar terbentuk di MySQL sebelum melanjutkan membuat seeder data contoh.

**Perhatian:**
Jika ada baris yang masih berstatus Pending atau gagal sebagian, perbaiki pesan error pada file migration terkait lalu jalankan ulang php artisan migrate.

```sh
php artisan migrate:status
```

### Buat file seeder ServiceSeeder

<!-- step: {"id":"modul1-4-3-buat-serviceseeder","module":1,"step":4,"part":3,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal proyek laundry.
2. File baru database/seeders/ServiceSeeder.php akan terbuat secara otomatis.

**Tujuannya:**
Menyiapkan class seeder khusus yang bertugas mengisi 5 layanan laundry awal agar halaman katalog langsung punya data realistis untuk diuji.

**Perhatian:**
Pembuatan file seeder belum mengisi data apa pun ke database sampai isinya dilengkapi dan dipanggil lewat DatabaseSeeder.

```sh
php artisan make:seeder ServiceSeeder
```

### Import model Service di ServiceSeeder

<!-- step: {"id":"modul1-5-1-serviceseeder","module":1,"step":5,"part":1,"mode":"import","method":null,"target":"database/seeders/ServiceSeeder.php"} -->
**Kerjakan di sini:**
1. Buka file database/seeders/ServiceSeeder.php.
2. Tambahkan import App\Models\Service; di bagian use atas class ServiceSeeder (klik "Pratinjau sisipkan kode").

**Tujuannya:**
Mengimpor class Service agar pemanggilan Service::create($item) di dalam method run() dikenali PHP tanpa namespace error.

**Perhatian:**
Pastikan import mengarah ke App\Models\Service, bukan Database\Seeders\Service.

```php
use App\Models\Service;
```

### Isi method run() pada ServiceSeeder dengan 5 layanan awal

<!-- step: {"id":"modul1-5-2-serviceseeder","module":1,"step":5,"part":2,"mode":"method-body","method":"run","target":"database/seeders/ServiceSeeder.php"} -->
**Kerjakan di sini:**
1. Masih di database/seeders/ServiceSeeder.php, arahkan kursor ke method public function run(): void.
2. Klik "Pratinjau sisipkan kode" atau ganti isi kurung kurawal run() dengan array $services dan perulangan Service::create($item), lalu simpan file.

**Tujuannya:**
Menyediakan 5 data layanan awal mencakup ketiga kategori (Kiloan, Express, Satuan) serta kedua satuan hitung (Kg dan Pcs) sehingga fitur hitung checkout Kg maupun Pcs langsung bisa diuji.

**Perhatian:**
Jangan ubah nilai category atau unit_type di luar enum migration, dan tulis price_per_unit sebagai integer polos (contoh 7000, tanpa titik pemisah atau tanda Rp).

```php
$services = [
    [
        'service_name' => 'Cuci Kering Setrika Reguler',
        'category' => 'Kiloan',
        'price_per_unit' => 7000,
        'unit_type' => 'Kg',
        'description' => 'Layanan cuci bersih, kering, dan setrika rapi wangi tahan lama estimasi pengerjaan 2 hari.',
    ],
    [
        'service_name' => 'Cuci Kilat Express 4 Jam',
        'category' => 'Express',
        'price_per_unit' => 12000,
        'unit_type' => 'Kg',
        'description' => 'Layanan cuci dan setrika kilat selesai dalam 4-6 jam, pakaian siap pakai seketika.',
    ],
    [
        'service_name' => 'Dry Cleaning Jas / Blazer',
        'category' => 'Satuan',
        'price_per_unit' => 25000,
        'unit_type' => 'Pcs',
        'description' => 'Pembersihan profesional untuk setelan jas, blazer, atau pakaian formal tanpa merusak bahan.',
    ],
    [
        'service_name' => 'Cuci Bed Cover Jumbo',
        'category' => 'Satuan',
        'price_per_unit' => 20000,
        'unit_type' => 'Pcs',
        'description' => 'Pencucian khusus selimut tebal dan bed cover ukuran besar agar bersih, bebas tungau, dan harum.',
    ],
    [
        'service_name' => 'Setrika Uap Express',
        'category' => 'Express',
        'price_per_unit' => 8000,
        'unit_type' => 'Kg',
        'description' => 'Jasa setrika uap licin dan rapi untuk pakaian kering yang butuh cepat rapi siap pakai.',
    ],
];

foreach ($services as $item) {
    Service::create($item);
}
```

### Buat migration tambahan kolom is_admin di tabel users

<!-- step: {"id":"laundry-admin-migration","module":1,"step":6,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal proyek laundry.
2. Flag --table=users memberi tahu Artisan bahwa migration ini mengubah tabel users yang sudah ada, bukan membuat tabel baru.

**Tujuannya:**
Menyiapkan file migration alter table untuk menambahkan kolom boolean is_admin pada tabel users sebagai pembeda akun admin dan pelanggan biasa.

**Perhatian:**
Jangan pernah mengedit langsung file migration lama 0001_01_01_000000_create_users_table.php karena file itu sudah berstatus Ran. Perubahan struktur tabel lama harus selalu lewat migration baru.

```sh
php artisan make:migration add_is_admin_to_users_table --table=users
```

### Isi file migration add_is_admin_to_users_table

<!-- step: {"id":"laundry-admin-migration-code","module":1,"step":6,"part":2,"mode":"file","method":null,"target":"database/migrations/xxxx_xx_xx_xxxxxx_add_is_admin_to_users_table.php"} -->
**Kerjakan di sini:**
1. Buka file migration baru yang berakhiran _add_is_admin_to_users_table.php di folder database/migrations/.
2. Ganti seluruh isi file dengan kode di bawah (atau gunakan "Pratinjau sisipkan kode"), lalu simpan file.

**Tujuannya:**
Menambahkan kolom boolean is_admin dengan nilai default false di method up() serta perintah rollback dropColumn('is_admin') di down(). Nilai default false memastikan user baru dari /register publik otomatis non-admin.

**Perhatian:**
Pastikan tipe kolomnya boolean dengan ->default(false) agar pengguna biasa tidak pernah terangkat menjadi admin secara tidak sengaja.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
```

### Import Service dan Hash di DatabaseSeeder

<!-- step: {"id":"modul1-6-1-databaseseeder","module":1,"step":6,"part":3,"mode":"import","method":null,"target":"database/seeders/DatabaseSeeder.php"} -->
**Kerjakan di sini:**
1. Buka file database/seeders/DatabaseSeeder.php.
2. Tambahkan dua baris import ini di bagian atas file di bawah namespace (pertahankan import User dan Seeder yang sudah ada).

**Tujuannya:**
Mengimpor model Service (untuk cek kondisi seeding awal) dan facade Hash (untuk mengenkripsi password akun admin uji secara aman).

**Perhatian:**
Jangan menghapus use App\Models\User; atau use Illuminate\Database\Seeder; yang sudah bawaan file tersebut.

```php
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
```

### Pastikan trait WithoutModelEvents aktif di DatabaseSeeder

<!-- step: {"id":"modul1-6-2-databaseseeder","module":1,"step":6,"part":4,"mode":"trait","method":null,"target":"database/seeders/DatabaseSeeder.php"} -->
**Kerjakan di sini:**
1. Masih di database/seeders/DatabaseSeeder.php, lihat baris pertama di dalam class DatabaseSeeder.
2. Pastikan trait use WithoutModelEvents; terpasang (klik "Pratinjau sisipkan kode" untuk mengecek atau menambahkannya).

**Tujuannya:**
Menjaga agar proses seeding awal berjalan cepat tanpa memicu event model tambahan saat menyisipkan data seed.

**Perhatian:**
Pastikan baris use Illuminate\Database\Console\Seeds\WithoutModelEvents; ada di area import atas jika belum terpasang.

```php
use WithoutModelEvents;
```

### Isi DatabaseSeeder dengan akun admin default dan ServiceSeeder

<!-- step: {"id":"modul1-6-3-databaseseeder","module":1,"step":6,"part":5,"mode":"method-body","method":"run","target":"database/seeders/DatabaseSeeder.php"} -->
**Kerjakan di sini:**
1. Masih di database/seeders/DatabaseSeeder.php, arahkan ke method public function run(): void.
2. Ganti isi kurung kurawal run() (hapus contoh factory user lama jika masih ada) dengan kode di bawah, lalu simpan file.

**Tujuannya:**
Menyiapkan akun admin uji (email: admin@gmail.com, password: password123) dengan is_admin = true menggunakan firstOrCreate dan pengecekan ! $admin->is_admin agar idempoten saat seeder dijalankan ulang, lalu menjalankan ServiceSeeder hanya jika tabel services masih kosong.

**Perhatian:**
Perhatikan is_admin di-assign langsung ($admin->is_admin = true) dan tidak dimasukkan ke $fillable User agar user register biasa tidak bisa menyuntikkan status admin lewat request form (Mass Assignment protection). Pastikan model Service dan User pada langkah berikutnya sudah dikonfigurasi sebelum menjalankan php artisan migrate --seed.

```php
$admin = User::firstOrCreate(['email' => 'admin@gmail.com'], [
    'name' => 'Admin Laundry',
    'password' => Hash::make('password123'),
]);

if (! $admin->is_admin) {
    $admin->is_admin = true;
    $admin->save();
}

if (! Service::exists()) {
    $this->call(ServiceSeeder::class);
}
```

### Tambahkan import HasFactory di model Service

<!-- step: {"id":"modul1-8-1-service","module":1,"step":7,"part":1,"mode":"import","method":null,"target":"app/Models/Service.php"} -->
**Kerjakan di sini:**
1. Buka file app/Models/Service.php.
2. Tambahkan use Illuminate\Database\Eloquent\Factories\HasFactory; di bagian use atas class Service.

**Tujuannya:**
Mengimpor trait HasFactory supaya model Service mendukung pembuatan data dummy/test via factory Laravel.

**Perhatian:**
Letakkan statement use di luar kurung kurawal class Service.

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
```

### Pasang trait HasFactory di dalam class Service

<!-- step: {"id":"modul1-8-2-service","module":1,"step":7,"part":2,"mode":"trait","method":null,"target":"app/Models/Service.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/Service.php, lihat baris awal di dalam class Service extends Model { ... }.
2. Tambahkan use HasFactory; di dalam body class.

**Tujuannya:**
Mengaktifkan fitur factory pada model Eloquent Service.

**Perhatian:**
Jangan keliru antara use untuk import namespace (di atas class) dan use untuk trait (di dalam class).

```php
use HasFactory;
```

### Atur nama tabel dan $guarded pada model Service

<!-- step: {"id":"modul1-8-3-service","module":1,"step":7,"part":3,"mode":"class-members","method":null,"target":"app/Models/Service.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/Service.php, tambahkan properti $table dan $guarded di dalam class Service.
2. Simpan file dengan Ctrl+S.

**Tujuannya:**
Menegaskan nama tabel services dan melindungi kolom id dari mass-assignment sambil mengizinkan Service::create()/update() mengisi service_name, category, price_per_unit, unit_type, dan description (baik saat ServiceSeeder dijalankan pada langkah berikutnya maupun setelah divalidasi di controller).

**Perhatian:**
Langkah ini wajib selesai sebelum menjalankan php artisan migrate --seed agar Service::create() di ServiceSeeder tidak gagal dengan MassAssignmentException. Jangan mencampurkan properti $fillable dan $guarded bersamaan di satu model yang sama.

```php
protected $table = 'services';

protected $guarded = ['id'];
```

### Import HasFactory dan HasMany di model Order

<!-- step: {"id":"modul1-8-4-order","module":1,"step":7,"part":4,"mode":"import","method":null,"target":"app/Models/Order.php"} -->
**Kerjakan di sini:**
1. Buka file app/Models/Order.php.
2. Tambahkan dua baris import di bawah di bagian atas class Order.

**Tujuannya:**
Mengimpor trait HasFactory dan class return type HasMany untuk mendefinisikan relasi satu Order memiliki banyak OrderDetail.

**Perhatian:**
Pastikan HasMany diimpor dari Illuminate\Database\Eloquent\Relations\HasMany.

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
```

### Pasang trait HasFactory di dalam class Order

<!-- step: {"id":"modul1-8-5-order","module":1,"step":7,"part":5,"mode":"trait","method":null,"target":"app/Models/Order.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/Order.php, tambahkan use HasFactory; di dalam kurung kurawal class Order.

**Tujuannya:**
Mengaktifkan dukungan model factory pada model Order.

**Perhatian:**
Pastikan trait use HasFactory; ditulis di dalam kurung kurawal class Order, bukan di area import namespace di atas class.

```php
use HasFactory;
```

### Atur $guarded dan relasi orderDetails() di model Order

<!-- step: {"id":"modul1-8-6-order","module":1,"step":7,"part":6,"mode":"class-members","method":null,"target":"app/Models/Order.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/Order.php, sisipkan properti $guarded dan method orderDetails() ke dalam class Order.
2. Simpan file (Ctrl+S).

**Tujuannya:**
Mendefinisikan relasi One-to-Many (Order hasMany OrderDetail) dengan foreign key order_id sehingga OrderController::store() dapat memanggil $order->orderDetails()->create(...) dan adminDashboard() dapat eager-load Order::with('orderDetails.service').

**Perhatian:**
Nama method wajib persis orderDetails (camelCase) agar pemanggilan eager loading orderDetails.service di OrderController tidak error.

```php
protected $guarded = ['id'];

public function orderDetails(): HasMany
{
    return $this->hasMany(OrderDetail::class, 'order_id');
}
```

### Import HasFactory dan BelongsTo di model OrderDetail

<!-- step: {"id":"modul1-8-7-orderdetail","module":1,"step":7,"part":7,"mode":"import","method":null,"target":"app/Models/OrderDetail.php"} -->
**Kerjakan di sini:**
1. Buka file app/Models/OrderDetail.php.
2. Tambahkan import HasFactory dan BelongsTo di area use atas class OrderDetail.

**Tujuannya:**
Mengimpor tipe relasi BelongsTo karena tabel anak order_details merujuk kembali ke induk Order dan Service.

**Perhatian:**
Gunakan Illuminate\Database\Eloquent\Relations\BelongsTo.

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
```

### Pasang trait HasFactory di dalam class OrderDetail

<!-- step: {"id":"modul1-8-8-orderdetail","module":1,"step":7,"part":8,"mode":"trait","method":null,"target":"app/Models/OrderDetail.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/OrderDetail.php, tambahkan use HasFactory; di awal tubuh class OrderDetail.

**Tujuannya:**
Mengaktifkan dukungan factory pada model OrderDetail.

**Perhatian:**
Letakkan use HasFactory; di dalam kurung kurawal class OrderDetail tepat sebelum properti $table.

```php
use HasFactory;
```

### Atur $table, $guarded, dan relasi belongsTo di OrderDetail

<!-- step: {"id":"modul1-8-9-orderdetail","module":1,"step":7,"part":9,"mode":"class-members","method":null,"target":"app/Models/OrderDetail.php"} -->
**Kerjakan di sini:**
1. Masih di app/Models/OrderDetail.php, tambahkan $table, $guarded, serta method service() dan order() di dalam class OrderDetail.
2. Simpan file (Ctrl+S).

**Tujuannya:**
Menghubungkan setiap item detail ke data layanannya ($detail->service->service_name / unit_type di dashboard) dan kembali ke pesanan induknya ($detail->order).

**Perhatian:**
Pastikan relasi service() mengarah ke foreign key service_id dan order() ke order_id.

```php
protected $table = 'order_details';

protected $guarded = ['id'];

public function service(): BelongsTo
{
    return $this->belongsTo(Service::class, 'service_id');
}

public function order(): BelongsTo
{
    return $this->belongsTo(Order::class, 'order_id');
}
```

### Tambahkan casting boolean is_admin di model User

<!-- step: {"id":"modul1-8-10-user-casts","module":1,"step":7,"part":10,"mode":"method-body","method":"casts","target":"app/Models/User.php"} -->
**Kerjakan di sini:**
1. Buka file app/Models/User.php, lalu cari method protected function casts(): array di bagian bawah class.
2. Gunakan "Pratinjau sisipkan kode" atau tambahkan baris 'is_admin' => 'boolean', di dalam array return method casts(), lalu simpan file.
3. Catatan penting: tetap jangan masukkan 'is_admin' ke daftar #[Fillable] / $fillable User agar pendaftar publik tidak bisa menipu sistem menjadi admin lewat payload form.

**Tujuannya:**
Memastikan Eloquent selalu mengonversi nilai kolom is_admin dari MySQL (angka tinyint 0 atau 1) menjadi tipe boolean PHP murni (false atau true) sehingga pengecekan strict $user->is_admin di Gate dan test berjalan konsisten.

**Perhatian:**
Jangan tambahkan 'is_admin' ke $fillable/Fillable di User.php; pemberian status admin hanya boleh lewat seeder atau Tinker. Jika nanti Breeze bertanya menimpa User.php, tolak atau pastikan baris cast ini tetap ada.

```php
return [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'is_admin' => 'boolean',
];
```

### Terapkan migration is_admin dan jalankan seeder ke database

<!-- step: {"id":"modul1-7-1-reset-dan-seed","module":1,"step":8,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Pastikan file migration add_is_admin_to_users_table, ServiceSeeder, DatabaseSeeder, serta konfigurasi model Service, Order, OrderDetail, dan User pada langkah sebelumnya sudah disimpan.
2. Jalankan perintah ini di terminal VS Code dari folder proyek laundry.

**Tujuannya:**
Menerapkan kolom baru users.is_admin ke MySQL dan sekaligus mengeksekusi DatabaseSeeder untuk membuat akun admin serta mengisi 5 layanan laundry awal.

**Perhatian:**
Jika muncul error MassAssignmentException pada Service, pastikan properti protected $guarded = ['id']; di app/Models/Service.php sudah disimpan. Jika muncul error "Unknown column is_admin", berarti migration add_is_admin_to_users_table belum tersimpan sebelum perintah ini dijalankan.

```sh
php artisan migrate --seed
```

## Modul 2 · Login admin dan CRUD layanan

### Install package Laravel Breeze dengan Composer

<!-- step: {"id":"modul2-1-1-install-breeze","module":2,"step":1,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Pastikan terminal berada di root folder laundry dan koneksi internet aktif.
2. Jalankan perintah ini untuk mengunduh package scaffolding autentikasi resmi laravel/breeze ke proyek.

**Tujuannya:**
Menyediakan generator autentikasi resmi Laravel Breeze (login, register, logout, reset password, dan profil) beserta komponen layout Tailwind bawaan.

**Perhatian:**
Langkah ini baru mengunduh paket ke vendor/; route serta halaman login/register baru akan terbuat setelah menjalankan artisan breeze:install di langkah berikutnya.

```sh
composer require laravel/breeze --dev
```

### Jalankan installer Breeze stack Blade

<!-- step: {"id":"modul2-2-1-setup-breeze","module":2,"step":2,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal proyek laundry.
2. Jika Breeze menanyakan pilihan stack tambahan (misalnya dark mode atau framework test PHPUnit/Pest), pilih sesuai kebutuhan (default PHPUnit: 0, dark mode: no/enter).

**Tujuannya:**
Menghasilkan controller auth di app/Http/Controllers/Auth/, tampilan Blade login/register/layout di resources/views/, konfigurasi Tailwind/Vite, dan route autentikasi di routes/auth.php.

**Perhatian:**
Breeze menulis ulang routes/web.php dan menyediakan view bawaan dashboard.blade.php serta navigation.blade.php (yang memang akan kita sesuaikan di Modul 4). Pastikan app/Models/User.php tidak kehilangan cast is_admin setelah proses selesai.

```sh
php artisan breeze:install
```

### Jalankan php artisan migrate setelah instalasi Breeze

<!-- step: {"id":"modul2-3-1-migrate","module":2,"step":3,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal proyek laundry setelah breeze:install blade selesai.
2. Jika terminal menampilkan "Nothing to migrate", itu normal dan menandakan seluruh tabel sudah mutakhir.

**Tujuannya:**
Memastikan tidak ada migration baru tertinggal setelah scaffolding Breeze terpasang sebelum memasang aset frontend.

**Perhatian:**
Jangan jalankan migrate:fresh pada tahap ini agar akun admin@gmail.com dan 5 layanan seeder di Modul 1 tidak ikut terhapus.

```sh
php artisan migrate
```

### Install dependensi Node.js dan Tailwind CSS

<!-- step: {"id":"modul2-3-2-npm-install","module":2,"step":3,"part":2,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal proyek laundry.
2. Tunggu hingga npm selesai mengunduh modul ke folder node_modules.

**Tujuannya:**
Mengunduh Vite, Tailwind CSS, @tailwindcss/forms, Alpine.js, dan plugin frontend yang didefinisikan package.json hasil Breeze.

**Perhatian:**
Jika ada masalah jaringan saat mengunduh, periksa koneksi lalu jalankan ulang npm install sampai selesai tanpa error.

```sh
npm install
```

### Kompilasi aset CSS dan JS dengan Vite build

<!-- step: {"id":"modul2-3-3-npm-build","module":2,"step":3,"part":3,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini tepat setelah npm install selesai.
2. Pastikan terbentuk file manifest di folder public/build/manifest.json tanpa pesan error merah.

**Tujuannya:**
Menghasilkan bundle CSS/JS produksi agar seluruh halaman autentikasi Breeze dan komponen x-app-layout dapat dirender dengan styling Tailwind tanpa error Vite manifest.

**Perhatian:**
Setiap kali nanti menambah class Tailwind baru di view yang belum pernah terkompilasi, jalankan ulang npm run build (atau nyalakan npm run dev di tab terminal kedua).

```sh
npm run build
```

### Import model, DB transaction, dan validasi di ServiceController

<!-- step: {"id":"modul2-4-1-servicecontroller","module":2,"step":4,"part":1,"mode":"import","method":null,"target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Buka file app/Http/Controllers/ServiceController.php hasil generate -mcr pada Modul 1.
2. Tambahkan import OrderDetail, DB, dan ValidationException di bagian atas class (use App\Models\Service; dan use Illuminate\Http\Request; biasanya sudah ada; tombol "Pratinjau sisipkan kode" tidak akan menggandakannya).

**Tujuannya:**
Mengimpor model OrderDetail (untuk cek apakah layanan pernah dipesan), facade DB (untuk transaksi dan row locking lockForUpdate), serta ValidationException untuk menolak perubahan satuan layanan yang terpakai.

**Perhatian:**
Jangan tambahkan import RedirectResponse atau View yang tidak terpakai jika method bawaan make:model -mcr di ServiceController tidak memakai return type hint eksplisit.

```php
use App\Models\OrderDetail;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
```

### Isi method index() di ServiceController untuk daftar layanan

<!-- step: {"id":"modul2-4-2-servicecontroller","module":2,"step":4,"part":2,"mode":"method-body","method":"index","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Di file app/Http/Controllers/ServiceController.php, cari method public function index().
2. Sisipkan kode di bawah ke dalam kurung kurawal method index() dengan "Pratinjau sisipkan kode", lalu simpan.

**Tujuannya:**
Mengambil daftar layanan laundry terbaru (latest()) dibagi 10 item per halaman (paginate(10)) lalu mengirimkannya ke view admin.services.index.

**Perhatian:**
View admin/services/index.blade.php nanti memanggil $services->links(), sehingga query di sini wajib memakai paginate(10), bukan get().

```php
$services = Service::latest()->paginate(10);

return view('admin.services.index', compact('services'));
```

### Isi method create() di ServiceController untuk form tambah

<!-- step: {"id":"modul2-4-3-servicecontroller","module":2,"step":4,"part":3,"mode":"method-body","method":"create","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/ServiceController.php, cari method public function create().
2. Masukkan baris return view ini ke dalam method create().

**Tujuannya:**
Menampilkan halaman formulir tambah layanan baru di resources/views/admin/services/create.blade.php saat admin menekan tombol Tambah Layanan.

**Perhatian:**
Pastikan file view resources/views/admin/services/create.blade.php dibuat di langkah berikutnya sebelum membuka rute /admin/services/create.

```php
return view('admin.services.create');
```

### Isi method store() di ServiceController untuk simpan layanan baru

<!-- step: {"id":"modul2-4-4-servicecontroller","module":2,"step":4,"part":4,"mode":"method-body","method":"store","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/ServiceController.php, cari method public function store(Request $request).
2. Sisipkan logika validasi, Service::create($data), dan redirect dengan pesan sukses ke dalam kurung kurawal store().

**Tujuannya:**
Memvalidasi seluruh input layanan baru (termasuk batas nilai enum category dan unit_type serta rentang integer SQL price_per_unit), menyimpannya ke database, dan kembali ke daftar layanan dengan flash message sukses.

**Perhatian:**
Gunakan selalu array $data hasil $request->validate(...) dan jangan pernah langsung mengirim $request->all() ke Service::create().

```php
$data = $request->validate([
    'service_name' => 'required|string|max:255',
    'category' => 'required|in:Kiloan,Satuan,Express',
    'price_per_unit' => 'required|integer|min:0|max:2147483647',
    'unit_type' => 'required|in:Kg,Pcs',
    'description' => 'required|string',
]);

Service::create($data);

return redirect()->route('services.index')->with('success', 'Data layanan berhasil ditambahkan!');
```

### Isi method edit() di ServiceController untuk form ubah

<!-- step: {"id":"modul2-4-5-servicecontroller","module":2,"step":4,"part":5,"mode":"method-body","method":"edit","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/ServiceController.php, cari method public function edit(Service $service).
2. Ganti isi kurung kurawal edit() dengan return view berikut.

**Tujuannya:**
Memanfaatkan Route Model Binding ($service) Laravel untuk langsung mengirimkan data layanan yang dipilih admin ke form edit.

**Perhatian:**
Nama parameter pada edit(Service $service) harus tetap $service agar cocok dengan parameter {service} dari Route::resource('/admin/services', ...).

```php
return view('admin.services.edit', compact('service'));
```

### Isi method update() dengan kunci perubahan satuan layanan terpakai

<!-- step: {"id":"modul2-4-6-servicecontroller","module":2,"step":4,"part":6,"mode":"method-body","method":"update","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/ServiceController.php, cari method public function update(Request $request, Service $service).
2. Masukkan kode validasi dan blok DB::transaction dengan lockForUpdate ke dalam kurung kurawal update(), lalu simpan file.

**Tujuannya:**
Memperbarui layanan laundry dengan pengaman bisnis kritis: jika layanan sudah pernah tercatat di OrderDetail, admin boleh memperbarui nama/harga/deskripsi, tetapi dilarang mengubah unit_type (Kg <-> Pcs) agar histori nota pesanan lama tidak berubah makna.

**Perhatian:**
Bungkus pengecekan OrderDetail dan update di dalam DB::transaction + lockForUpdate() untuk mencegah race condition jika ada transaksi checkout masuk bersamaan saat admin mengedit satuan.

```php
$data = $request->validate([
    'service_name' => 'required|string|max:255',
    'category' => 'required|in:Kiloan,Satuan,Express',
    'price_per_unit' => 'required|integer|min:0|max:2147483647',
    'unit_type' => 'required|in:Kg,Pcs',
    'description' => 'required|string',
]);

DB::transaction(function () use ($service, $data) {
    $service = Service::lockForUpdate()->findOrFail($service->id);

    if ($service->unit_type !== $data['unit_type'] && OrderDetail::where('service_id', $service->id)->exists()) {
        throw ValidationException::withMessages(['unit_type' => 'Satuan tidak bisa diubah karena layanan sudah pernah dipesan.']);
    }

    $service->update($data);
});

return redirect()->route('services.index')->with('success', 'Data layanan berhasil diperbarui!');
```

### Isi method destroy() dengan proteksi hapus layanan yang sudah dipesan

<!-- step: {"id":"modul2-4-7-servicecontroller","module":2,"step":4,"part":7,"mode":"method-body","method":"destroy","target":"app/Http/Controllers/ServiceController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/ServiceController.php, cari method public function destroy(Service $service).
2. Masukkan blok transaksi pengecekan OrderDetail dan penghapusan aman ini ke dalam method destroy(), lalu simpan.

**Tujuannya:**
Mencegah terhapusnya layanan yang sudah dipakai di order_details (karena migration memiliki onDelete cascade yang dapat ikut menghilangkan rincian transaksi lama), sekaligus mengembalikan pesan error ramah bila layanan sedang terpakai.

**Perhatian:**
Tanpa pengecekan OrderDetail::where(...) ini, menekan Hapus pada layanan yang sudah pernah dipesan akan ikut menghapus baris order_details historis secara diam-diam akibat cascade.

```php
$deleted = DB::transaction(function () use ($service) {
    $service = Service::lockForUpdate()->findOrFail($service->id);

    if (OrderDetail::where('service_id', $service->id)->exists()) {
        return false;
    }

    return $service->delete();
});

if (! $deleted) {
    return back()->with('error', 'Layanan sudah dipakai di pesanan dan tidak bisa dihapus.');
}

return redirect()->route('services.index')->with('success', 'Data layanan berhasil dihapus!');
```

### Buat tampilan daftar layanan admin (admin/services/index.blade.php)

<!-- step: {"id":"modul2-5-1-view-index","module":2,"step":5,"part":1,"mode":"file","method":null,"target":"resources/views/admin/services/index.blade.php"} -->
**Kerjakan di sini:**
1. Klik "Buka file tujuan" untuk membuat dan membuka file baru resources/views/admin/services/index.blade.php (folder admin/services/ akan terbuat otomatis saat dibuat via panel atau VS Code).
2. Gunakan "Pratinjau sisipkan kode" atau salin seluruh markup Blade di bawah ke dalam file, lalu simpan (Ctrl+S).

**Tujuannya:**
Menampilkan tabel manajemen layanan laundry untuk admin (kolom Nama, Kategori, Tarif per Unit, dan Aksi) lengkap dengan alert sukses/error, tombol + Tambah Layanan, format harga Rupiah, aksi Edit & Hapus (dengan method DELETE + konfirmasi browser), status kosong (empty state) bila belum ada data, serta link pagination.

**Perhatian:**
Pastikan tag form hapus selalu menyertakan @csrf dan @method('DELETE') karena HTML form standar hanya mendukung GET dan POST.

```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Data Layanan</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('services.create') }}" class="bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 text-white px-4 py-2 rounded mb-4 inline-block font-semibold">+ Tambah Layanan</a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto bg-white border mt-4 shadow-sm rounded-lg">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 border-b text-gray-700 text-sm">
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Tarif per Unit</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr class="border-b text-center text-sm">
                        <td class="p-3 font-semibold">{{ $service->service_name }}</td>
                        <td class="p-3">{{ $service->category }}</td>
                        <td class="p-3 font-bold text-green-700">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}</td>
                        <td class="p-3">
                            <a href="{{ route('services.edit', $service->id) }}" class="text-blue-600 hover:underline mr-3 font-semibold">Edit</a>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-600">Belum ada data layanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    </div>
</x-app-layout>
```

### Buat tampilan form tambah layanan (admin/services/create.blade.php)

<!-- step: {"id":"modul2-5-2-view-create","module":2,"step":5,"part":2,"mode":"file","method":null,"target":"resources/views/admin/services/create.blade.php"} -->
**Kerjakan di sini:**
1. Buat atau buka file resources/views/admin/services/create.blade.php.
2. Isi seluruh file dengan markup Blade form ini, lalu simpan (Ctrl+S).

**Tujuannya:**
Menyediakan form input terstruktur menuju route('services.store') lengkap dengan banner error validasi (@if($errors->any())), helper old() agar ketikan tidak hilang saat gagal validasi, indikator fokus keyboard yang jelas, serta pilihan category dan unit_type yang persis cocok dengan enum tabel.

**Perhatian:**
Atribut name pada input (service_name, category, price_per_unit, unit_type, description) harus identik dengan kunci validasi di ServiceController::store().

```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Layanan</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if($errors->any())
            <ul class="mb-4 list-disc rounded bg-red-100 p-4 pl-8 text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('services.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            <div class="mb-4">
                <label for="service_name" class="block font-medium mb-1">Nama Layanan</label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
            </div>
            <div class="mb-4">
                <label for="category" class="block font-medium mb-1">Kategori</label>
                <select id="category" name="category" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
                    <option value="Kiloan" @selected(old('category') === 'Kiloan')>Kiloan</option>
                    <option value="Satuan" @selected(old('category') === 'Satuan')>Satuan</option>
                    <option value="Express" @selected(old('category') === 'Express')>Express</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="unit_type" class="block font-medium mb-1">Satuan</label>
                <select id="unit_type" name="unit_type" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
                    <option value="Kg" @selected(old('unit_type') === 'Kg')>Kg</option>
                    <option value="Pcs" @selected(old('unit_type') === 'Pcs')>Pcs</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="price_per_unit" class="block font-medium mb-1">Tarif per Unit (Rp)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" min="0" max="2147483647" value="{{ old('price_per_unit') }}" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block font-medium mb-1">Deskripsi</label>
                <textarea id="description" name="description" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" rows="3" required>{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-800 focus:ring-offset-2 text-white px-6 py-2.5 rounded font-semibold">Simpan Data</button>
        </form>
    </div>
</x-app-layout>
```

### Buat tampilan form edit layanan (admin/services/edit.blade.php)

<!-- step: {"id":"modul2-5-3-view-edit","module":2,"step":5,"part":3,"mode":"file","method":null,"target":"resources/views/admin/services/edit.blade.php"} -->
**Kerjakan di sini:**
1. Buat atau buka file resources/views/admin/services/edit.blade.php.
2. Isi seluruh file dengan markup Blade edit form berikut dan simpan (Ctrl+S).

**Tujuannya:**
Menampilkan form ubah layanan menuju route('services.update', $service) dengan directive @method('PUT'), terisi otomatis nilai sebelumnya via old('field', $service->field), serta menampilkan banner error jika perubahan satuan unit_type ditolak server.

**Perhatian:**
Sertakan selalu @csrf dan @method('PUT') agar Laravel mengarahkan form submit ini ke method update(), bukan store().

```html
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Layanan</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if($errors->any())
            <ul class="mb-4 list-disc rounded bg-red-100 p-4 pl-8 text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('services.update', $service->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="service_name" class="block font-medium mb-1">Nama Layanan</label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name', $service->service_name) }}" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
            </div>
            <div class="mb-4">
                <label for="category" class="block font-medium mb-1">Kategori</label>
                <select id="category" name="category" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
                    <option value="Kiloan" @selected(old('category', $service->category) === 'Kiloan')>Kiloan</option>
                    <option value="Satuan" @selected(old('category', $service->category) === 'Satuan')>Satuan</option>
                    <option value="Express" @selected(old('category', $service->category) === 'Express')>Express</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="unit_type" class="block font-medium mb-1">Satuan</label>
                <select id="unit_type" name="unit_type" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
                    <option value="Kg" @selected(old('unit_type', $service->unit_type) === 'Kg')>Kg</option>
                    <option value="Pcs" @selected(old('unit_type', $service->unit_type) === 'Pcs')>Pcs</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="price_per_unit" class="block font-medium mb-1">Tarif per Unit (Rp)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" min="0" max="2147483647" value="{{ old('price_per_unit', $service->price_per_unit) }}" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block font-medium mb-1">Deskripsi</label>
                <textarea id="description" name="description" class="w-full border border-gray-500 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-700" rows="3" required>{{ old('description', $service->description) }}</textarea>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 text-white px-6 py-2.5 rounded font-semibold">Update Data</button>
        </form>
    </div>
</x-app-layout>
```

## Modul 3 · Pemesanan pelanggan

### Import model, return type, DB, dan exception di OrderController

<!-- step: {"id":"modul3-1-1-ordercontroller","module":3,"step":1,"part":1,"mode":"import","method":null,"target":"app/Http/Controllers/OrderController.php"} -->
**Kerjakan di sini:**
1. Buka file app/Http/Controllers/OrderController.php.
2. Tambahkan keenam baris import ini di area use atas class OrderController (Request biasanya sudah diimpor oleh Artisan).

**Tujuannya:**
Mengimpor Order, Service, RedirectResponse, DB, ValidationException, dan View yang dipakai seluruh method OrderController baik untuk halaman publik maupun dashboard admin.

**Perhatian:**
Berbeda dengan ServiceController, di OrderController method adminDashboard() dan updateStatus() memakai return type eksplisit : View dan : RedirectResponse sehingga import keduanya di sini wajib ada.

```php
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
```

### Isi method index() di OrderController untuk katalog publik

<!-- step: {"id":"modul3-1-2-ordercontroller","module":3,"step":1,"part":2,"mode":"method-body","method":"index","target":"app/Http/Controllers/OrderController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/OrderController.php, cari method public function index().
2. Ganti isi kurung kurawal index() dengan dua baris berikut dan simpan.

**Tujuannya:**
Mengambil seluruh data layanan laundry dari tabel services lalu mengirimkannya ke tampilan katalog publik resources/views/customer/index.blade.php.

**Perhatian:**
Pastikan nama variabel yang dikirim ke compact() adalah 'services' karena view customer/index.blade.php melakukan loop pada $services.

```php
$services = Service::all();

return view('customer.index', compact('services'));
```

### Isi method store() di OrderController untuk checkout pesanan aman

<!-- step: {"id":"modul3-1-3-ordercontroller","module":3,"step":1,"part":3,"mode":"method-body","method":"store","target":"app/Http/Controllers/OrderController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/OrderController.php, cari method public function store(Request $request).
2. Sisipkan seluruh logika validasi, filter item > 0, transaksi DB, sharedLock(), perhitungan subtotal di server, dan penyimpanan order + orderDetails ke dalam kurung kurawal store(), lalu simpan.

**Tujuannya:**
Memproses checkout pelanggan dengan aman: memvalidasi nama & format nomor WhatsApp (regex 8-15 digit dengan opsional +), menolak jika tidak ada item > 0, mengambil harga resmi dari tabel services via sharedLock() (tidak pernah mempercayai total dari browser), membatasi overflow integer SQL, dan menyimpan Order serta OrderDetails secara atomik.

**Perhatian:**
Jangan pernah menerima input total_price atau price_per_unit dari request form browser karena mudah dimanipulasi lewat Inspect Element. Server harus selalu menghitung subtotal dan total dari harga database.

```php
$data = $request->validate([
    'customer_name' => 'required|string|max:255',
    'whatsapp_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
    'items' => 'required|array',
    'items.*' => 'nullable|integer|min:0|max:2147483647',
]);

$items = array_filter($data['items'], fn ($quantity) => $quantity > 0);

if (empty($items)) {
    return back()->withInput()->with('error', 'Pilih minimal satu layanan!');
}

DB::transaction(function () use ($data, $items) {
    $services = Service::whereKey(array_keys($items))->sharedLock()->get()->keyBy('id');

    if (array_diff_key($items, $services->all())) {
        throw ValidationException::withMessages(['items' => 'Ada layanan yang sudah tidak tersedia.']);
    }

    $order = Order::create([
        'customer_name' => $data['customer_name'],
        'whatsapp_number' => $data['whatsapp_number'],
        'total_price' => 0,
        'status' => 'Antrean',
    ]);

    $total = 0;

    foreach ($items as $serviceId => $quantity) {
        $subtotal = $services[$serviceId]->price_per_unit * $quantity;
        $total += $subtotal;

        if ($total > 2_147_483_647) {
            throw ValidationException::withMessages(['items' => 'Jumlah pesanan terlalu besar.']);
        }

        $order->orderDetails()->create([
            'service_id' => $serviceId,
            'qty_or_weight' => $quantity,
            'subtotal' => $subtotal,
        ]);
    }

    $order->update(['total_price' => $total]);
});

return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat dan masuk antrean.');
```

### Tambahkan method adminDashboard() di OrderController

<!-- step: {"id":"modul3-1-4-ordercontroller","module":3,"step":1,"part":4,"mode":"class-members","method":null,"target":"app/Http/Controllers/OrderController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/OrderController.php, tambahkan method adminDashboard(): View ini di dalam class OrderController (sebelum kurung kurawal penutup class).
2. Simpan file (Ctrl+S).

**Tujuannya:**
Mengambil semua pesanan terbaru lengkap dengan relasi bersarang orderDetails dan service memakai eager loading Order::with('orderDetails.service')->latest()->get() lalu merendernya di view dashboard.

**Perhatian:**
Pemakaian with('orderDetails.service') mencegah masalah N+1 query saat dashboard menampilkan puluhan pesanan dan rincian layanannya sekaligus.

```php
public function adminDashboard(): View
{
    $orders = Order::with('orderDetails.service')->latest()->get();

    return view('dashboard', compact('orders'));
}
```

### Tambahkan method updateStatus() di OrderController

<!-- step: {"id":"modul3-1-5-ordercontroller","module":3,"step":1,"part":5,"mode":"class-members","method":null,"target":"app/Http/Controllers/OrderController.php"} -->
**Kerjakan di sini:**
1. Masih di app/Http/Controllers/OrderController.php, tambahkan method updateStatus(Request $request, Order $order): RedirectResponse di dalam class OrderController.
2. Simpan file (Ctrl+S).

**Tujuannya:**
Menangani perubahan status pengerjaan pesanan oleh admin dari form dropdown (terbatas hanya ke Antrean, Dicuci, atau Selesai) lalu kembali ke dashboard dengan notifikasi nomor pesanan berhasil diperbarui.

**Perhatian:**
Validasi in:Antrean,Dicuci,Selesai wajib persis dengan enum tabel orders agar database tidak menolak nilai status tak dikenal.

```php
public function updateStatus(Request $request, Order $order): RedirectResponse
{
    $data = $request->validate(['status' => 'required|in:Antrean,Dicuci,Selesai']);
    $order->update($data);

    return back()->with('success', 'Status pesanan #'.$order->id.' berhasil diperbarui!');
}
```

### Buat tampilan katalog dan formulir checkout (customer/index.blade.php)

<!-- step: {"id":"modul3-2-1-view-index","module":3,"step":2,"part":1,"mode":"file","method":null,"target":"resources/views/customer/index.blade.php"} -->
**Kerjakan di sini:**
1. Buat atau buka file resources/views/customer/index.blade.php.
2. Masukkan seluruh kode HTML/Blade + skrip kalkulator estimasi total di bawah ke dalam file, lalu simpan (Ctrl+S).

**Tujuannya:**
Menampilkan halaman depan aplikasi untuk pelanggan: header navigasi (tautan Admin saat guest, atau Dashboard Admin / Akun Saya saat sudah login), tombol filter kategori, kartu layanan lengkap dengan harga & satuan (beserta status kosong bila layanan/kategori belum ada), input jumlah/berat (items[service_id]), form identitas pelanggan + WhatsApp menuju customer.checkout, serta modal konfirmasi pesanan dengan kalkulator estimasi total real-time dan dukungan tutup modal via tombol Escape.

**Perhatian:**
Perhatikan JavaScript di halaman ini hanya menampilkan estimasi total agar nyaman bagi pelanggan; perhitungan biaya resmi yang disimpan ke database tetap dilakukan penuh oleh OrderController::store().

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Laundry</title>
    <link rel="icon" href="data:,">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Layanan Laundry</h1>
            <p class="text-gray-600 text-sm mt-1">Pilih layanan, isi perkiraan berat atau jumlah, lalu masukkan nomor WhatsApp.</p>
            @guest
                <a href="{{ route('login') }}" class="mt-2 inline-block text-sm text-blue-700 underline">Admin</a>
            @else
                <a href="{{ auth()->user()->can('manage-laundry') ? route('dashboard') : route('profile.edit') }}" class="mt-2 inline-block text-sm text-blue-700 underline">
                    {{ auth()->user()->can('manage-laundry') ? 'Dashboard Admin' : 'Akun Saya' }}
                </a>
            @endguest
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button type="button" onclick="filterCategory('all', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">Semua Layanan</button>
            <button type="button" onclick="filterCategory('Kiloan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-700 hover:bg-gray-200 border border-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">Kiloan</button>
            <button type="button" onclick="filterCategory('Satuan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-700 hover:bg-gray-200 border border-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">Satuan</button>
            <button type="button" onclick="filterCategory('Express', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-700 hover:bg-gray-200 border border-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">Express</button>
        </div>

        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST">
            @csrf

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">1. Informasi Pemesan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" maxlength="255" required placeholder="Masukkan nama pemesan" class="w-full border border-gray-500 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-700 focus:outline-none">
                    </div>
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-semibold text-gray-700 mb-1">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number" required maxlength="16" pattern="\+?[0-9]{8,15}" value="{{ old('whatsapp_number') }}" placeholder="Contoh: 081234567890" class="w-full border border-gray-500 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-700 focus:outline-none">
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Layanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div class="service-card bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between" data-category="{{ $service->category }}">
                        <div>
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2.5 py-0.5 rounded">{{ $service->category }}</span>
                                    <span class="font-bold text-green-700">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg item-name">{{ $service->service_name }}</h3>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $service->description }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t">
                            <label for="service-{{ $service->id }}" class="block text-xs font-semibold text-gray-700 mb-1">
                                {{ $service->unit_type === 'Kg' ? 'Perkiraan berat (Kg)' : 'Jumlah (Pcs)' }}
                            </label>
                            <input type="number" id="service-{{ $service->id }}" name="items[{{ $service->id }}]" min="0" step="1" value="{{ old('items.'.$service->id, 0) }}" data-unit="{{ $service->unit_type }}" data-name="{{ $service->service_name }}" data-price="{{ $service->price_per_unit }}" class="item-qty w-full border border-gray-500 rounded-lg px-3 py-1.5 text-center font-bold text-gray-700 focus:ring-2 focus:ring-blue-700 focus:outline-none">
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 bg-white p-6 rounded-xl border text-center text-gray-600">
                        Belum ada layanan laundry yang tersedia.
                    </div>
                @endforelse
            </div>
            <p id="emptyCategoryMessage" class="hidden text-center text-gray-600 bg-white p-6 rounded-xl border mt-4">Belum ada layanan pada kategori ini.</p>

            <div class="mt-8 text-right">
                <button type="button" id="review-order" onclick="showConfirmationModal()" class="bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">Pesan Sekarang</button>
            </div>

            <div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 id="confirm-title" class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Konfirmasi Pesanan</h3>
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between"><span class="font-semibold">Nama:</span> <span id="modalName" class="text-gray-900 font-bold"></span></div>
                        <div class="flex justify-between"><span class="font-semibold">WhatsApp:</span> <span id="modalWhatsapp" class="text-gray-900 font-bold"></span></div>
                    </div>
                    <div class="border-t border-b py-3 mb-4 max-h-48 overflow-y-auto">
                        <p class="font-semibold text-xs text-gray-600 uppercase mb-2">Rincian Item</p>
                        <ul id="modalItemList" class="space-y-2 text-sm"></ul>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-6">
                        <span>Total Pembayaran:</span>
                        <span id="modalTotalPrice" class="text-green-700 text-xl">Rp 0</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" id="cancel-confirmation" onclick="closeConfirmationModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-600 text-gray-800 font-semibold py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="w-1/2 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-800 text-white font-bold py-2.5 rounded-xl shadow transition">Ya, Kirim Pesanan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function filterCategory(category, element) {
            document.querySelectorAll('.btn-category').forEach(btn => {
                btn.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-700 hover:bg-gray-200 border border-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2";
            });
            element.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2";
            const cards = document.querySelectorAll('.service-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                const isVisible = category === 'all' || cardCategory === category;
                card.style.display = isVisible ? 'flex' : 'none';
                if (isVisible) {
                    visibleCount++;
                }
            });
            const emptyCategoryMessage = document.getElementById('emptyCategoryMessage');
            if (emptyCategoryMessage && cards.length > 0) {
                emptyCategoryMessage.classList.toggle('hidden', visibleCount > 0);
            }
        }

        function showConfirmationModal() {
            const form = document.getElementById('orderForm');
            if (!form.reportValidity()) {
                return;
            }

            const itemList = document.getElementById('modalItemList');
            itemList.replaceChildren();
            let grandTotal = 0;

            document.querySelectorAll('.item-qty').forEach(input => {
                const quantity = Number(input.value);
                if (quantity <= 0) {
                    return;
                }

                const subtotal = quantity * Number(input.dataset.price);
                grandTotal += subtotal;

                const item = document.createElement('li');
                item.textContent = `${input.dataset.name} (${quantity} ${input.dataset.unit}) · Rp ${subtotal.toLocaleString('id-ID')}`;
                itemList.appendChild(item);
            });

            if (!itemList.children.length) {
                alert('Pilih minimal satu layanan.');
                return;
            }

            document.getElementById('modalName').textContent = form.elements.customer_name.value.trim();
            document.getElementById('modalWhatsapp').textContent = form.elements.whatsapp_number.value.trim();
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            const modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('cancel-confirmation').focus();
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('flex');
            document.getElementById('review-order').focus();
            modal.classList.add('hidden');
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !document.getElementById('confirmModal').classList.contains('hidden')) {
                closeConfirmationModal();
            }
        });
    </script>
</body>
</html>
```

## Modul 4 · Dashboard, hak akses, dan route

### Ganti tampilan dashboard bawaan Breeze dengan dashboard admin laundry

<!-- step: {"id":"modul4-1-1-view-dashboard","module":4,"step":1,"part":1,"mode":"file","method":null,"target":"resources/views/dashboard.blade.php"} -->
**Kerjakan di sini:**
1. Buka file resources/views/dashboard.blade.php yang sebelumnya dibuat oleh Breeze.
2. Ganti seluruh isinya menggunakan kode di bawah (atau lewat "Pratinjau sisipkan kode"), lalu simpan (Ctrl+S).

**Tujuannya:**
Menghadirkan halaman operasional admin di /dashboard dan /admin/dashboard: tombol cepat Kelola Layanan dan Lihat Layanan Customer, serta tabel pesanan masuk (# ID, Pelanggan, WhatsApp, Rincian Pesanan, Total Harga, Status, Aksi Status) lengkap dengan status kosong (empty state) dan form PATCH ubah status per baris pesanan.

**Perhatian:**
Setiap form ubah status pesanan memakai method POST + @method('PATCH') ke route('admin.orders.updateStatus', $order) dan menyertakan @csrf.

```html
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pesanan Masuk') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm transition">
                    Kelola Layanan
                </a>
                <a href="{{ route('customer.index') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 shadow-sm transition">
                    Lihat Layanan Customer
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="p-4 border-b"># ID</th>
                                <th class="p-4 border-b">Pelanggan</th>
                                <th class="p-4 border-b">WhatsApp</th>
                                <th class="p-4 border-b">Rincian Pesanan</th>
                                <th class="p-4 border-b">Total Harga</th>
                                <th class="p-4 border-b">Status</th>
                                <th class="p-4 border-b text-center">Aksi Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-bold text-gray-700">#{{ $order->id }}</td>
                                    <td class="p-4 font-medium">{{ $order->customer_name }}</td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full text-xs">
                                            {{ $order->whatsapp_number }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                                            @foreach($order->orderDetails as $detail)
                                                <li>
                                                    <strong>{{ $detail->service->service_name ?? 'Layanan' }}</strong>
                                                    {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? '' }}
                                                    <span class="text-xs text-gray-600">(Rp {{ number_format($detail->subtotal) }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-4 font-bold text-green-700">
                                        Rp {{ number_format($order->total_price) }}
                                    </td>
                                    <td class="p-4">
                                        @if($order->status === 'Antrean')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded">Antrean</span>
                                        @elseif($order->status === 'Selesai')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded">Selesai</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" aria-label="Status pesanan {{ $order->id }}" onchange="this.form.submit()" class="text-xs border border-gray-500 rounded p-1.5 bg-white shadow-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-700">
                                                @foreach(['Antrean', 'Dicuci', 'Selesai'] as $status)
                                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-600">Belum ada pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
```

### Import model User dan facade Gate di AppServiceProvider

<!-- step: {"id":"laundry-gate-import","module":4,"step":2,"part":1,"mode":"import","method":null,"target":"app/Providers/AppServiceProvider.php"} -->
**Kerjakan di sini:**
1. Buka file app/Providers/AppServiceProvider.php.
2. Tambahkan import User dan Gate di bagian use atas class AppServiceProvider.

**Tujuannya:**
Menyiapkan class User dan Gate agar aturan otorisasi admin (manage-laundry) dapat didaftarkan secara terpusat di service provider aplikasi.

**Perhatian:**
Gunakan Illuminate\Support\Facades\Gate, bukan class Gate lain.

```php
use App\Models\User;
use Illuminate\Support\Facades\Gate;
```

### Daftarkan Gate manage-laundry di method boot()

<!-- step: {"id":"laundry-gate-code","module":4,"step":2,"part":2,"mode":"method-body","method":"boot","target":"app/Providers/AppServiceProvider.php"} -->
**Kerjakan di sini:**
1. Masih di app/Providers/AppServiceProvider.php, temukan method public function boot(): void.
2. Masukkan baris Gate::define di dalam kurung kurawal boot() lalu simpan (Ctrl+S).

**Tujuannya:**
Mendefinisikan aturan hak akses terpusat bernama manage-laundry yang mengembalikan true hanya jika user yang login memiliki is_admin bernilai truthy/true, sehingga pengguna non-admin otomatis ditolak HTTP 403 saat mencoba membuka halaman admin.

**Perhatian:**
Nama Gate manage-laundry ini nantinya dipanggil di middleware route (can:manage-laundry), Auth controller redirect, dan navigation.blade.php; pastikan ejaannya persis sama.

```php
Gate::define('manage-laundry', fn (User $user): bool => (bool) $user->is_admin);
```

### Import OrderController dan ServiceController di routes/web.php

<!-- step: {"id":"modul4-2-1-web","module":4,"step":2,"part":3,"mode":"import","method":null,"target":"routes/web.php"} -->
**Kerjakan di sini:**
1. Buka file routes/web.php.
2. Tambahkan kedua statement use controller ini di bawah use ProfileController / use Illuminate\Support\Facades\Route;

**Tujuannya:**
Mengimpor kedua controller utama agar definisi route publik dan grup route admin di routes/web.php bisa memanggil [OrderController::class, ...] dan ServiceController::class secara ringkas.

**Perhatian:**
Pertahankan baris use App\Http\Controllers\ProfileController; dan require __DIR__.'/auth.php'; yang sudah dibuat oleh Breeze.

```php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
```

### Daftarkan route katalog publik dan checkout pelanggan

<!-- step: {"id":"modul4-2-2-web","module":4,"step":2,"part":4,"mode":"manual","method":null,"target":"routes/web.php"} -->
**Kerjakan di sini:**
1. Masih di routes/web.php, cari dan hapus/ganti blok Route::get('/', function () { return view('welcome'); }); bawaan instalasi Laravel.
2. Salin kedua baris route di bawah ke posisi paling atas daftar route, lalu simpan file (Ctrl+S).

**Tujuannya:**
Menjadikan katalog pemesanan laundry (OrderController::index, nama customer.index) sebagai halaman utama /, serta membuka endpoint POST /checkout (customer.checkout) untuk menerima kiriman form pesanan pelanggan tanpa perlu login terlebih dahulu.

**Perhatian:**
Langkah ini bermode manual karena posisi blok welcome berbeda-beda: pastikan route GET / bawaan view('welcome') tidak dibiarkan tertinggal di file supaya tidak menimpa customer.index.

```php
Route::get('/', [OrderController::class, 'index'])->name('customer.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('customer.checkout');
```

### HAPUS route /dashboard bawaan Breeze sebelum membuat grup admin

<!-- step: {"id":"modul4-2-3-web","module":4,"step":2,"part":5,"mode":"manual","method":null,"target":"routes/web.php"} -->
**Kerjakan di sini:**
1. Cari blok kode di bawah ini di dalam file routes/web.php (biasanya berada tepat di atas grup Route::middleware('auth')->group(...) milik /profile).
2. HAPUS seluruh blok 3 baris Route::get('/dashboard', ...) ini dari routes/web.php. Jangan dihapus grup /profile atau require __DIR__.'/auth.php';.
3. Simpan file setelah blok ini dihapus agar tidak bentrok dengan route admin /dashboard di langkah berikutnya.

**Tujuannya:**
Membersihkan route /dashboard bawaan Breeze yang mengizinkan semua user login masuk ke view dashboard tanpa pemeriksaan Gate is_admin.

**Perhatian:**
Penting: kode di kotak bawah adalah contoh kode LAMA yang HARUS DIHAPUS, bukan ditambahkan. Jika dibiarkan atau terduplikasi, pengguna biasa berisiko lolos melihat view dashboard tanpa data $orders (Undefined variable $orders) atau tanpa Gate manage-laundry.

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
```

### Tambahkan grup route admin dengan middleware can:manage-laundry

<!-- step: {"id":"modul4-2-4-web","module":4,"step":2,"part":6,"mode":"manual","method":null,"target":"routes/web.php"} -->
**Kerjakan di sini:**
1. Di dalam routes/web.php (tepat di posisi tempat kamu menghapus route dashboard lama, atau setelah route profil auth), tempelkan seluruh blok grup admin di bawah.
2. Pastikan require __DIR__.'/auth.php'; tetap ada di bagian paling bawah file, lalu simpan (Ctrl+S).

**Tujuannya:**
Melindungi seluruh fitur operasional admin (/dashboard, /admin/dashboard, PATCH update status pesanan, dan CRUD resource /admin/services) dengan 3 lapis middleware sekaligus: wajib login (auth), email terverifikasi (verified), dan lolos Gate admin (can:manage-laundry).

**Perhatian:**
Resource services di sini memakai ->except('show') karena detail per item sudah langsung terlihat di tabel index; sementara nama route resource-nya tetap services.* (services.index, services.create, services.store, services.edit, services.update, services.destroy).

```php
Route::middleware(['auth', 'verified', 'can:manage-laundry'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [OrderController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::resource('/admin/services', ServiceController::class)->except('show');
});
```

### Verifikasi susunan route dan middleware via route:list

<!-- step: {"id":"modul4-2-5-route-list","module":4,"step":2,"part":7,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan perintah ini di terminal VS Code dari folder laundry setelah selesai menyimpan routes/web.php dan AppServiceProvider.php.
2. Periksa bahwa GET / memanggil OrderController@index (customer.index), POST /checkout memanggil OrderController@store (customer.checkout), GET dashboard hanya ada SATU route menuju OrderController@adminDashboard dengan middleware can:manage-laundry, serta /admin/services.* terdaftar lengkap.

**Tujuannya:**
Memastikan seluruh penamaan route, controller tujuan, dan perlindungan middleware can:manage-laundry benar-benar aktif serta tidak ada route duplikat yang tertinggal.

**Perhatian:**
Jika di output terminal terlihat dua baris berbeda untuk URI dashboard atau salah satunya masih berupa Closure, berarti route bawaan Breeze di langkah sebelumnya belum terhapus.

```sh
php artisan route:list
```

### Atur redirect pasca-login admin vs non-admin di AuthenticatedSessionController

<!-- step: {"id":"laundry-auth-1","module":4,"step":3,"part":1,"mode":"method-body","method":"store","target":"app/Http/Controllers/Auth/AuthenticatedSessionController.php"} -->
**Kerjakan di sini:**
1. Buka file app/Http/Controllers/Auth/AuthenticatedSessionController.php.
2. Cari method public function store(LoginRequest $request): RedirectResponse.
3. Ganti isi kurung kurawal store() dengan kode di bawah via "Pratinjau sisipkan kode", lalu simpan (Ctrl+S).

**Tujuannya:**
Mengarahkan admin (yang lolos Gate manage-laundry) ke halaman intended atau dashboard admin, sementara akun biasa tetap dialihkan ke katalog customer.index (dan url.intended ke URL admin dibersihkan agar tidak memantul ke 403).

**Perhatian:**
Jangan menghapus $request->authenticate(); dan $request->session()->regenerate(); karena keduanya menjalankan validasi login Breeze serta mencegah serangan session fixation.

```php
$request->authenticate();

$request->session()->regenerate();

if (! $request->user()->can('manage-laundry')) {
    $request->session()->forget('url.intended');

    return redirect()->route('customer.index');
}

return redirect()->intended(route('dashboard', absolute: false));
```

### Arahkan akun hasil register publik ke katalog customer.index

<!-- step: {"id":"laundry-auth-2","module":4,"step":3,"part":2,"mode":"method-body","method":"store","target":"app/Http/Controllers/Auth/RegisteredUserController.php"} -->
**Kerjakan di sini:**
1. Buka file app/Http/Controllers/Auth/RegisteredUserController.php.
2. Cari method public function store(Request $request): RedirectResponse.
3. Perbarui isi method store() dengan kode di bawah (perubahan utama di baris redirect terakhir) dan simpan file.

**Tujuannya:**
Menjaga agar setiap pengguna baru yang mendaftar lewat /register langsung masuk ke halaman katalog pelanggan (customer.index) tanpa mencoba membuka /dashboard yang terproteksi 403.

**Perhatian:**
Tetap pertahankan Hash::make, event(new Registered($user)), dan kolom is_admin tetap memakai default false dari database tanpa pernah dibaca dari request input.

```php
$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
]);

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

event(new Registered($user));

Auth::login($user);

return redirect()->route('customer.index');
```

### Arahkan redirect verifikasi email sesuai hak akses user

<!-- step: {"id":"laundry-auth-3","module":4,"step":3,"part":3,"mode":"method-body","method":"__invoke","target":"app/Http/Controllers/Auth/VerifyEmailController.php"} -->
**Kerjakan di sini:**
1. Buka file app/Http/Controllers/Auth/VerifyEmailController.php.
2. Cari method public function __invoke(EmailVerificationRequest $request): RedirectResponse.
3. Ganti isi kurung kurawal __invoke() dengan kode ini, lalu simpan.

**Tujuannya:**
Memastikan apabila akun melakukan verifikasi email, tujuan redirect setelah terverifikasi otomatis menyesuaikan apakah akun tersebut berhak masuk dashboard admin atau kembali ke customer.index.

**Perhatian:**
Perhatikan tanda tanya ?verified=1 tetap dipertahankan sesuai alur notifikasi sukses bawaan Laravel Breeze.

```php
if ($request->user()->hasVerifiedEmail()) {
    return redirect()->intended(route($request->user()->can('manage-laundry') ? 'dashboard' : 'customer.index', absolute: false).'?verified=1');
}

if ($request->user()->markEmailAsVerified()) {
    event(new Verified($request->user()));
}

return redirect()->intended(route($request->user()->can('manage-laundry') ? 'dashboard' : 'customer.index', absolute: false).'?verified=1');
```

### Sesuaikan navbar Breeze agar menampilkan menu sesuai hak akses

<!-- step: {"id":"laundry-navigation","module":4,"step":3,"part":4,"mode":"file","method":null,"target":"resources/views/layouts/navigation.blade.php"} -->
**Kerjakan di sini:**
1. Buka file resources/views/layouts/navigation.blade.php.
2. Ganti seluruh isi file menggunakan "Pratinjau sisipkan kode" atau salin-tempel penuh kode Blade di bawah, lalu simpan (Ctrl+S).

**Tujuannya:**
Menyesuaikan link utama di bilah navigasi desktop maupun menu hamburger mobile agar otomatis menampilkan label "Dashboard" (ke route dashboard) bagi admin dan "Layanan Laundry" (ke route customer.index) bagi user non-admin.

**Perhatian:**
Menyesuaikan tampilan navigasi di Blade hanya membantu kenyamanan antarmuka; keamanan sesungguhnya tetap ditegakkan dari server melalui Gate can:manage-laundry pada AppServiceProvider dan routes/web.php.

```html
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route(auth()->user()->can('manage-laundry') ? 'dashboard' : 'customer.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route(auth()->user()->can('manage-laundry') ? 'dashboard' : 'customer.index')" :active="request()->routeIs('dashboard', 'customer.index')">
                        {{ auth()->user()->can('manage-laundry') ? 'Dashboard' : 'Layanan Laundry' }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" aria-label="Menu navigasi" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route(auth()->user()->can('manage-laundry') ? 'dashboard' : 'customer.index')" :active="request()->routeIs('dashboard', 'customer.index')">
                {{ auth()->user()->can('manage-laundry') ? 'Dashboard' : 'Layanan Laundry' }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
```

### Build aset akhir, jalankan server lokal, dan lakukan uji alur menyeluruh

<!-- step: {"id":"laundry-run","module":4,"step":4,"part":1,"mode":"terminal","method":null,"target":"terminal"} -->
**Kerjakan di sini:**
1. Jalankan kedua perintah di bawah pada terminal proyek laundry (npm run build mengompilasi semua class Tailwind dari view baru, lalu php artisan serve menyalakan server lokal di http://127.0.0.1:8000).
2. Lakukan checklist pengujian manual di browser dari awal sampai akhir:
   a) Halaman publik (/): pastikan 5 layanan awal tampil; uji submit kosong untuk memastikan muncul error; lalu pesan layanan Kg dan Pcs dengan nomor WhatsApp valid (8-15 digit), tekan Pesan Sekarang untuk memeriksa modal konfirmasi (uji tombol Batal maupun tombol Escape untuk menutupnya), lalu kirim pesanan dan pastikan muncul notif sukses masuk antrean.
   b) Register & proteksi non-admin: buka /register, buat akun pelanggan baru, pastikan langsung diarahkan ke /. Lalu coba ketik manual URL /dashboard dan /admin/services di address bar: server WAJIB menolak dengan 403 THIS ACTION IS UNAUTHORIZED.
   c) Login admin: logout, lalu masuk di /login memakai email admin@gmail.com dan password password123, pastikan langsung masuk ke /dashboard.
   d) Operasi dashboard & CRUD admin: ubah status pesanan pelanggan tadi dari Antrean -> Dicuci -> Selesai. Buka /admin/services, tambah layanan baru, edit layanan, lalu uji pengaman bisnis (ubah satuan unit_type layanan yang sudah pernah dipesan -> harus ditolak error validasi; coba hapus layanan yang sudah pernah dipesan -> harus ditolak pesan error). Terakhir hapus layanan baru yang belum pernah dipesan -> berhasil.

**Tujuannya:**
Memverifikasi keseluruhan sistem Laundry Express berjalan mulus secara end-to-end mulai dari kompilasi aset, pengalaman pemesanan pelanggan, pemisahan otorisasi user vs admin, hingga integritas bisnis CRUD dan pembaruan status pesanan.

**Perhatian:**
Biarkan terminal yang menjalankan php artisan serve tetap terbuka selama kamu menguji di browser (tekan Ctrl+C bila ingin menghentikan server). Jika tampilan Tailwind terlihat berantakan atau tidak berubah, hentikan sejenak lalu jalankan ulang npm run build dan refresh browser (Ctrl+F5).

```sh
npm run build
php artisan serve
```
