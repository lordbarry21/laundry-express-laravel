<?php

// Mengimpor Facade Route dari framework Laravel untuk mendefinisikan rute URL
use App\Http\Controllers\Admin\DashboardController;
// Mengimpor OrderController untuk menangani rute halaman pelanggan
use App\Http\Controllers\Admin\OrderAdminController;
// Mengimpor AuthController untuk menangani rute login dan logout admin
use App\Http\Controllers\Admin\ServiceController;
// Mengimpor DashboardController untuk panel ringkasan admin
use App\Http\Controllers\AuthController;
// Mengimpor ServiceController untuk CRUD master tarif & layanan
use App\Http\Controllers\OrderController;
// Mengimpor OrderAdminController untuk pengelolaan transaksi dan update progres cuci
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTE SISTEM PENDAFTARAN LAYANAN LAUNDRY
| Bergaya Human Junior Developer dengan penjelasan rinci pada tiap baris
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE PUBLIK / SISI PELANGGAN (CUSTOMER)
// ==========================================

// Rute beranda: menampilkan katalog layanan, form pendaftaran pemesanan, dan tracking status
Route::get('/', [OrderController::class, 'index'])->name('home');

// Rute POST: memproses pengiriman data form pendaftaran pesanan laundry
Route::post('/pesan', [OrderController::class, 'store'])->name('orders.store');

// Rute GET: menampilkan nota bukti transaksi pesanan beserta link WhatsApp dan tombol cetak struk
Route::get('/nota/{order}', [OrderController::class, 'show'])->name('orders.show');

// ==========================================
// 2. RUTE AUTENTIKASI ADMIN
// ==========================================

// Rute GET: menampilkan formulir halaman login admin
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');

// Rute POST: memproses data username dan password untuk autentikasi login
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Rute POST: memproses logout admin dan menghapus sesi
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. RUTE KHUSUS PANEL ADMINISTRATOR
// Dilindungi middleware 'auth' agar hanya admin yang sudah login yang dapat mengakses
// ==========================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Rute Dashboard ringkasan indikator transaksi dan status
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rute CRUD Master Layanan (Tarif & Paket Cuci) ---
    // Menampilkan daftar master paket layanan
    Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
    // Menyimpan paket layanan baru
    Route::post('/layanan', [ServiceController::class, 'store'])->name('services.store');
    // Memperbarui data paket layanan
    Route::put('/layanan/{service}', [ServiceController::class, 'update'])->name('services.update');
    // Menghapus paket layanan
    Route::delete('/layanan/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    // --- Rute Pengelolaan Pesanan & Update Status Cuci ---
    // Menampilkan daftar seluruh transaksi cucian dan filter status
    Route::get('/pesanan', [OrderAdminController::class, 'index'])->name('orders.index');
    // Memperbarui status progres pengerjaan cuci ('Antrean' -> 'Dicuci' -> 'Selesai')
    Route::patch('/pesanan/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.updateStatus');
    // Menghapus data transaksi pesanan
    Route::delete('/pesanan/{order}', [OrderAdminController::class, 'destroy'])->name('orders.destroy');
});
