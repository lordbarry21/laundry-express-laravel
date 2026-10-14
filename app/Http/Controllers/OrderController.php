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
