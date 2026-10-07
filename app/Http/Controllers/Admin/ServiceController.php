<?php

// Menentukan namespace lokasi ServiceController di folder App\Http\Controllers\Admin

namespace App\Http\Controllers\Admin;

// Mengimpor class Controller dasar
use App\Http\Controllers\Controller;
// Mengimpor class Request untuk menangani form HTTP
use App\Models\Service;
// Mengimpor Model Service untuk memanipulasi data master layanan
use Illuminate\Http\Request;

// Mendefinisikan ServiceController untuk mengelola operasi CRUD master layanan (tarif & paket cuci)
class ServiceController extends Controller
{
    /**
     * Menampilkan daftar seluruh paket layanan yang terdaftar
     */
    public function index()
    {
        // Mengambil semua data layanan dari database diurutkan dari yang terbaru
        $services = Service::latest()->get();

        // Mengirimkan variabel $services ke tampilan Blade admin.services.index
        return view('admin.services.index', compact('services'));
    }

    /**
     * Menyimpan paket layanan baru yang ditambahkan admin ke database
     */
    public function store(Request $request)
    {
        // Melakukan validasi input form tambah layanan
        $validated = $request->validate([
            // Nama layanan wajib diisi, maksimal 100 karakter
            'service_name' => 'required|string|max:100',
            // Kategori harus salah satu dari: Kiloan, Satuan, atau Express
            'category' => 'required|in:Kiloan,Satuan,Express',
            // Tarif harga wajib berupa angka integer minimal 0
            'price_per_unit' => 'required|integer|min:0',
            // Jenis satuan harus salah satu dari: Kg atau Pcs
            'unit_type' => 'required|in:Kg,Pcs',
            // Deskripsi layanan boleh kosong (opsional)
            'description' => 'nullable|string',
        ]);

        // Menyimpan data paket layanan baru ke tabel services menggunakan Eloquent
        Service::create($validated);

        // Mengalihkan kembali ke halaman kelola layanan dengan pesan notifikasi sukses
        return redirect()->route('admin.services.index')
            ->with('success', 'Paket layanan baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui data paket layanan yang diedit oleh admin
     */
    public function update(Request $request, Service $service)
    {
        // Melakukan validasi data edit layanan
        $validated = $request->validate([
            // Nama layanan wajib diisi
            'service_name' => 'required|string|max:100',
            // Kategori harus valid
            'category' => 'required|in:Kiloan,Satuan,Express',
            // Tarif harga per unit
            'price_per_unit' => 'required|integer|min:0',
            // Jenis satuan ukuran
            'unit_type' => 'required|in:Kg,Pcs',
            // Deskripsi penjelasan paket
            'description' => 'nullable|string',
        ]);

        // Memperbarui atribut model Service dengan data inputan baru
        $service->update($validated);

        // Mengalihkan kembali dengan notifikasi sukses
        return redirect()->route('admin.services.index')
            ->with('success', 'Paket layanan berhasil diperbarui!');
    }

    /**
     * Menghapus paket layanan dari sistem
     */
    public function destroy(Service $service)
    {
        // Menghapus rekaman data layanan dari tabel services
        $service->delete();

        // Mengalihkan kembali ke daftar layanan dengan pesan sukses
        return redirect()->route('admin.services.index')
            ->with('success', 'Paket layanan berhasil dihapus dari sistem!');
    }
}
