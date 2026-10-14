<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota Pemesanan #{{ $order->id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased py-8 px-4">

    <div class="max-w-xl mx-auto space-y-4">

        <div class="flex justify-between items-center print:hidden">
            <a href="{{ route('order.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                &larr; Buat Pesanan Baru
            </a>
            <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold py-1.5 px-3 rounded">
                Cetak Nota
            </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5">
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Nota Pendaftaran Laundry</h1>
                    <p class="text-xs text-gray-500">Laundry Express &bull; No. Order: #{{ $order->id }}</p>
                </div>
                <div>
                    @if ($order->status === 'Antrean')
                        <span class="bg-yellow-100 text-yellow-800 border border-yellow-300 px-2.5 py-1 rounded text-xs font-bold">
                            Antrean
                        </span>
                    @elseif ($order->status === 'Dicuci')
                        <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2.5 py-1 rounded text-xs font-bold">
                            Dicuci
                        </span>
                    @else
                        <span class="bg-green-100 text-green-800 border border-green-300 px-2.5 py-1 rounded text-xs font-bold">
                            Selesai
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-500 uppercase font-semibold block">Pelanggan:</span>
                    <span class="font-bold text-gray-900 text-sm block">{{ $order->customer_name }}</span>
                    <span class="font-mono text-gray-600">WA: {{ $order->whatsapp_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-gray-500 uppercase font-semibold block">Waktu Transaksi:</span>
                    <span class="font-medium text-gray-800 block">{{ $order->created_at->format('d/m/Y') }}</span>
                    <span class="text-gray-500">{{ $order->created_at->format('H:i') }} WIB</span>
                </div>
            </div>

            <div>
                <table class="w-full border border-gray-200 rounded-md text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                        <tr>
                            <th class="p-2 text-left">Layanan</th>
                            <th class="p-2 text-center">Tarif</th>
                            <th class="p-2 text-center">Jumlah / Berat</th>
                            <th class="p-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->orderDetails as $detail)
                            <tr>
                                <td class="p-2">
                                    <div class="font-semibold text-gray-900">{{ $detail->service->service_name ?? 'Layanan' }}</div>
                                    <span class="text-[10px] text-gray-500">{{ $detail->service->category ?? '-' }}</span>
                                </td>
                                <td class="p-2 text-center">Rp {{ number_format($detail->service->price_per_unit ?? 0, 0, ',', '.') }}</td>
                                <td class="p-2 text-center font-medium">{{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }}</td>
                                <td class="p-2 text-right font-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="p-2 text-right font-bold text-gray-700">Total Biaya:</td>
                            <td class="p-2 text-right font-bold text-indigo-600 text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-xs text-gray-500 italic">
                * Simpan nota ini sebagai bukti saat penyerahan dan pengambilan pakaian cucian.
            </p>
        </div>

    </div>

</body>
</html>