<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota Pemesanan #{{ $order->id }} - Laundry Express</title>

    <!-- Fonts figtree (bawaan Laravel Breeze) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Asset CSS & JS Bawaan Laravel Breeze via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center">
        
        <div class="max-w-2xl w-full space-y-6">

            <!-- Tombol Kembali / Navigasi Atas (Tidak ikut tercetak) -->
            <div class="flex justify-between items-center no-print">
                <a href="{{ route('order.create') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                    &larr; Buat Pesanan Baru
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-wider hover:bg-gray-700 transition shadow">
                    Cetak Nota Transaksi
                </button>
            </div>

            <!-- KARTU NOTA TRANSAKSI -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden print-card">
                
                <!-- Header Nota -->
                <div class="bg-indigo-600 p-6 text-white text-center sm:text-left sm:flex sm:justify-between sm:items-center">
                    <div>
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="h-8 w-8 bg-white text-indigo-600 font-extrabold rounded-md flex items-center justify-center text-sm">
                                L
                            </span>
                            <h2 class="text-xl font-bold tracking-tight">Laundry Express</h2>
                        </div>
                        <p class="text-xs text-indigo-100 mt-1">Bukti Pendaftaran Layanan & Nota Cuci Kilat</p>
                    </div>
                    <div class="mt-4 sm:mt-0 text-center sm:text-right">
                        <span class="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-bold uppercase tracking-wider">
                            Status: {{ $order->status }}
                        </span>
                        <div class="text-xs text-indigo-200 mt-1">No. Nota: #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>

                <!-- Isi Rincian Transaksi -->
                <div class="p-6 sm:p-8 space-y-6">

                    <!-- Info Pelanggan & Waktu -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-gray-100 text-sm">
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Nama Pelanggan</span>
                            <div class="font-bold text-gray-900 text-base">{{ $order->customer_name }}</div>
                            <div class="text-gray-600 font-mono text-xs mt-0.5">WhatsApp: {{ $order->whatsapp_number }}</div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-xs text-gray-500 uppercase font-semibold">Waktu Transaksi</span>
                            <div class="font-medium text-gray-900">{{ $order->created_at->format('d F Y') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    <!-- Tabel Item Layanan -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Rincian Paket Cuci</h3>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600">Layanan</th>
                                        <th scope="col" class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600">Tarif</th>
                                        <th scope="col" class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600">Jumlah / Berat</th>
                                        <th scope="col" class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($order->orderDetails as $detail)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">{{ $detail->service->service_name ?? 'Layanan' }}</div>
                                                <span class="inline-block text-[10px] text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-medium">
                                                    {{ $detail->service->category ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap text-gray-600">
                                                Rp {{ number_format($detail->service->price_per_unit ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap font-medium text-gray-900">
                                                {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }}
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap font-bold text-gray-900">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700 text-sm">
                                            Total Biaya:
                                        </td>
                                        <td class="px-4 py-3 text-right font-extrabold text-indigo-600 text-lg">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Keterangan Progres & Petunjuk Pelanggan -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-xs text-indigo-900 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <span>Informasi Status Pesanan:</span>
                        </div>
                        <p class="leading-relaxed">
                            Pesanan Anda saat ini berstatus <strong>{{ $order->status }}</strong>. Silakan serahkan pakaian Anda ke kasir / gerai laundry atau konfirmasi penjemputan via nomor WhatsApp di atas. Status akan diperbarui oleh admin menjadi <em>Dicuci</em> hingga <em>Selesai</em>.
                        </p>
                    </div>

                </div>

                <!-- Footer Nota -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                    <span>Terima kasih telah mempercayakan pakaian Anda pada Laundry Express.</span>
                    <span class="font-mono text-gray-400">ID: {{ $order->id }}-{{ $order->created_at->timestamp }}</span>
                </div>

            </div>

            <!-- Tombol Aksi Tambahan (Tidak dicetak) -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center items-center no-print">
                <a href="{{ route('order.create') }}" class="w-full sm:w-auto text-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                    + Daftar Pesanan Baru
                </a>
                <a href="https://wa.me/?text=Halo%20Admin%20Laundry%20Express,%20saya%20sudah%20mendaftar%20dengan%20nomor%20nota%20%23{{ $order->id }}%20atas%20nama%20{{ urlencode($order->customer_name) }}." target="_blank" class="w-full sm:w-auto text-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold shadow transition">
                    Konfirmasi WhatsApp ke Admin
                </a>
            </div>

        </div>

    </div>

</body>
</html>
