<?php

// Menentukan namespace lokasi DashboardController di folder App\Http\Controllers\Admin

namespace App\Http\Controllers\Admin;

// Mengimpor class Controller dasar
use App\Http\Controllers\Controller;
// Mengimpor Model Order untuk menghitung statistik pesanan
use App\Models\Order;
// Mengimpor Model Service untuk menghitung jumlah paket layanan
use App\Models\Service;

// Mendefinisikan class DashboardController untuk menampilkan ringkasan data admin
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard ringkasan statistik laundry
     */
    public function index()
    {
        // Menghitung total keseluruhan transaksi pesanan yang masuk
        $totalOrders = Order::count();

        // Menghitung total akumulasi nominal pendapatan dari semua pesanan
        $totalRevenue = Order::sum('total_price');

        // Menghitung total pesanan yang masih berada dalam antrean awal
        $totalAntrean = Order::where('status', 'Antrean')->count();

        // Menghitung total pesanan yang sedang diproses cuci
        $totalDicuci = Order::where('status', 'Dicuci')->count();

        // Menghitung total pesanan yang sudah selesai dikerjakan
        $totalSelesai = Order::where('status', 'Selesai')->count();

        // Menghitung total ragam paket layanan yang terdaftar
        $totalServices = Service::count();

        // Mengambil 5 data pesanan terbaru untuk ditampilkan di tabel ringkasan dashboard
        $recentOrders = Order::latest()->take(5)->get();

        // Mengirimkan semua data statistik ke view admin.dashboard
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalAntrean',
            'totalDicuci',
            'totalSelesai',
            'totalServices',
            'recentOrders'
        ));
    }
}
