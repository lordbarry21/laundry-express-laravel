<?php

// Menentukan namespace controller di dalam folder App\Http\Controllers

namespace App\Http\Controllers;

// Mengimpor class Request untuk menangani HTTP Request dari form
use App\Models\Order;
// Mengimpor Model Service untuk mengambil data master paket cuci
use App\Models\OrderDetail;
// Mengimpor Model Order untuk menyimpan dan menampilkan data pesanan
use App\Models\Service;
// Mengimpor Model OrderDetail untuk mencatat rincian per item pesanan
use Illuminate\Http\Request;
// Mengimpor DB facade untuk transaksi database yang aman
use Illuminate\Support\Facades\DB;

// Mendefinisikan class OrderController bergaya junior developer yang rapi dan mudah dipahami
class OrderController extends Controller
{
    /**
     * Menampilkan halaman utama pelanggan (katalog layanan, formulir pemesanan, dan tracking status)
     */
    public function index(Request $request)
    {
        // Mengambil seluruh daftar layanan dari tabel services diurutkan berdasarkan kategori
        $services = Service::orderBy('category', 'asc')->get();

        // Menyiapkan variabel penampung hasil pencarian status pesanan
        $ordersTrack = null;

        // Memeriksa apakah pengguna mengisi kolom pencarian pelacakan pesanan
        if ($request->filled('keyword_tracking')) {
            // Mengambil kata kunci pencarian dari input URL GET
            $keyword = trim($request->keyword_tracking);
            // Mencari data order berdasarkan nomor ID nota atau nomor WhatsApp
            $ordersTrack = Order::where('id', $keyword)
                ->orWhere('whatsapp_number', 'like', "%{$keyword}%")
                ->latest()
                ->get();
        }

        // Mengirimkan variabel data layanan dan data tracking ke view customer.index
        return view('customer.index', compact('services', 'ordersTrack'));
    }

    /**
     * Memproses form pendaftaran pemesanan laundry baru dari pelanggan
     */
    public function store(Request $request)
    {
        // Melakukan validasi input data diri pelanggan dan pilihan layanan yang dikirim
        $request->validate([
            // Nama pelanggan wajib diisi berupa teks maksimal 100 karakter
            'customer_name' => 'required|string|max:100',
            // Nomor WhatsApp wajib diisi
            'whatsapp_number' => 'required|string|max:20',
            // Pilihan layanan harus berupa array dan minimal ada 1 pilihan
            'service_id' => 'required|array|min:1',
            // Jumlah kuantitas atau berat harus berupa array
            'qty_or_weight' => 'required|array|min:1',
        ], [
            // Pesan kustom jika validasi gagal
            'customer_name.required' => 'Nama lengkap wajib diisi!',
            'whatsapp_number.required' => 'Nomor WhatsApp wajib diisi!',
            'service_id.required' => 'Silakan pilih minimal 1 paket layanan!',
        ]);

        // Mengambil array service_id dan qty_or_weight dari request
        $serviceIds = $request->service_id;
        $quantities = $request->qty_or_weight;

        // Menghitung akumulasi total harga dari semua item yang dipilih
        $totalPrice = 0;
        // Menyiapkan array sementara untuk menampung data baris detail
        $itemsToInsert = [];

        // Melakukan perulangan untuk setiap baris layanan yang dipilih pelanggan
        for ($i = 0; $i < count($serviceIds); $i++) {
            // Memastikan ID layanan berupa integer
            $srvId = (int) $serviceIds[$i];
            // Memastikan jumlah berat/unit minimal bernilai 1
            $qty = isset($quantities[$i]) ? max(1, (int) $quantities[$i]) : 1;

            // Mengambil data master layanan dari database berdasarkan ID
            $service = Service::find($srvId);

            // Jika layanan ditemukan di database
            if ($service) {
                // Menghitung subtotal dengan mengalikan harga satuan dan kuantitas
                $subtotal = $service->price_per_unit * $qty;
                // Menambahkan ke akumulasi total belanja
                $totalPrice += $subtotal;

                // Menyimpan data item ke array
                $itemsToInsert[] = [
                    'service_id' => $srvId,
                    'qty_or_weight' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        // Memeriksa jika tidak ada item yang valid
        if (empty($itemsToInsert)) {
            // Mengembalikan pelanggan ke halaman sebelumnya dengan pesan kesalahan
            return back()->with('error', 'Layanan yang dipilih tidak valid!')->withInput();
        }

        // Menggunakan Database Transaction agar penyimpanan data header dan detail berjalan konsisten
        $order = DB::transaction(function () use ($request, $totalPrice, $itemsToInsert) {
            // 1. Menyimpan data pesanan baru ke tabel orders
            $newOrder = Order::create([
                'customer_name' => $request->customer_name,
                'whatsapp_number' => $request->whatsapp_number,
                'total_price' => $totalPrice,
                'status' => 'Antrean', // Status awal default adalah Antrean
            ]);

            // 2. Menyimpan setiap rincian item ke tabel order_details
            foreach ($itemsToInsert as $item) {
                OrderDetail::create([
                    'order_id' => $newOrder->id,
                    'service_id' => $item['service_id'],
                    'qty_or_weight' => $item['qty_or_weight'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Mengembalikan objek order yang baru dibuat
            return $newOrder;
        });

        // Mengalihkan pelanggan ke halaman nota transaksi dengan pesan sukses
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Pesanan laundry berhasil didaftarkan! Silakan simpan nota ini.');
    }

    /**
     * Menampilkan halaman nota kuitansi bukti pemesanan untuk pelanggan
     */
    public function show(Order $order)
    {
        // Memuat relasi orderDetails beserta data master service masing-masing
        $order->load('orderDetails.service');

        // Mengirimkan objek order ke view customer.nota
        return view('customer.nota', compact('order'));
    }
}
