<?php

// Menentukan namespace lokasi OrderAdminController di folder App\Http\Controllers\Admin

namespace App\Http\Controllers\Admin;

// Mengimpor class Controller dasar
use App\Http\Controllers\Controller;
// Mengimpor class Request untuk menangani HTTP input dan filter
use App\Models\Order;
// Mengimpor Model Order untuk mengelola transaksi pesanan
use Illuminate\Http\Request;

// Mendefinisikan class OrderAdminController untuk mengelola pesanan dan update progres cuci
class OrderAdminController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi pesanan cucian dengan filter status & pencarian
     */
    public function index(Request $request)
    {
        // Memulai query Eloquent untuk model Order beserta relasi detail dan servicenya
        $query = Order::with('orderDetails.service');

        // Memeriksa jika admin memfilter berdasarkan status tertentu ('Antrean', 'Dicuci', 'Selesai')
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Memeriksa jika admin mencari berdasarkan kata kunci (ID, nama pelanggan, atau no WA)
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('id', $keyword)
                    ->orWhere('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp_number', 'like', "%{$keyword}%");
            });
        }

        // Mengambil hasil pesanan terurut dari yang terbaru
        $orders = $query->latest()->get();

        // Mengirimkan variabel $orders ke view admin.orders.index
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Memperbarui progres status pengerjaan cuci ('Antrean' -> 'Dicuci' -> 'Selesai')
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Melakukan validasi input status agar sesuai dengan enum
        $request->validate([
            'status' => 'required|in:Antrean,Dicuci,Selesai',
        ]);

        // Mengupdate kolom status pada model Order
        $order->update([
            'status' => $request->status,
        ]);

        // Mengalihkan kembali dengan notifikasi sukses
        return redirect()->back()
            ->with('success', "Status pesanan #{$order->id} berhasil diubah menjadi: {$order->status}");
    }

    /**
     * Menghapus transaksi pesanan laundry dari basis data
     */
    public function destroy(Order $order)
    {
        // Menyimpan nomor ID nota untuk keperluan pesan notifikasi
        $orderId = $order->id;

        // Menghapus data order (order_details akan terhapus otomatis via onDelete cascade)
        $order->delete();

        // Mengalihkan kembali dengan pesan sukses
        return redirect()->route('admin.orders.index')
            ->with('success', "Pesanan #{$orderId} berhasil dihapus dari sistem!");
    }
}
