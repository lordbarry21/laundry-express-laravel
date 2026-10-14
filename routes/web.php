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
