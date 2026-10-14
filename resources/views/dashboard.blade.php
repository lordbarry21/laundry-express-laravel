<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Rekap Transaksi Laundry') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KARTU STATISTIK (WARNA FRAMEWORK) -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Total Pesanan</span>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</div>
                </div>

                <div class="bg-white border border-yellow-200 rounded-lg p-4">
                    <span class="text-xs font-semibold text-yellow-700 uppercase">Antrean</span>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['antrean'] }}</div>
                </div>

                <div class="bg-white border border-blue-200 rounded-lg p-4">
                    <span class="text-xs font-semibold text-blue-700 uppercase">Sedang Dicuci</span>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['dicuci'] }}</div>
                </div>

                <div class="bg-white border border-green-200 rounded-lg p-4">
                    <span class="text-xs font-semibold text-green-700 uppercase">Selesai</span>
                    <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['selesai'] }}</div>
                </div>

                <div class="bg-white border border-indigo-200 rounded-lg p-4 col-span-2 md:col-span-1">
                    <span class="text-xs font-semibold text-indigo-700 uppercase">Omzet Selesai</span>
                    <div class="text-base font-bold text-indigo-700 mt-1">Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- FILTER & DAFTAR PESANAN -->
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4 mb-4">
                    <h3 class="font-bold text-gray-800 text-sm">Daftar Transaksi Pesanan Masuk</h3>

                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-gray-500 font-medium">Filter:</span>
                        <a href="{{ route('dashboard') }}" class="px-2.5 py-1 rounded {{ !request('status') ? 'bg-indigo-600 text-white font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Antrean']) }}" class="px-2.5 py-1 rounded {{ request('status') === 'Antrean' ? 'bg-yellow-500 text-white font-semibold' : 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' }}">
                            Antrean ({{ $stats['antrean'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Dicuci']) }}" class="px-2.5 py-1 rounded {{ request('status') === 'Dicuci' ? 'bg-blue-600 text-white font-semibold' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                            Dicuci ({{ $stats['dicuci'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Selesai']) }}" class="px-2.5 py-1 rounded {{ request('status') === 'Selesai' ? 'bg-green-600 text-white font-semibold' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
                            Selesai ({{ $stats['selesai'] }})
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border border-gray-200 rounded-md">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase font-semibold">
                            <tr>
                                <th class="p-3">No. Order</th>
                                <th class="p-3">Pelanggan & WA</th>
                                <th class="p-3">Rincian Layanan</th>
                                <th class="p-3">Total Biaya</th>
                                <th class="p-3">Status Saat Ini</th>
                                <th class="p-3">Update Progres Cuci</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="font-bold text-gray-900 block">#{{ $order->id }}</span>
                                        <span class="text-gray-500 text-[11px]">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900 block">{{ $order->customer_name }}</span>
                                        <span class="font-mono text-gray-600 text-[11px]">{{ $order->whatsapp_number }}</span>
                                    </td>
                                    <td class="p-3">
                                        @foreach ($order->orderDetails as $detail)
                                            <div>
                                                {{ $detail->service->service_name ?? 'Layanan' }}
                                                <span class="text-gray-500">({{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }})</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="p-3 whitespace-nowrap font-bold text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($order->status === 'Antrean')
                                            <span class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-2 py-0.5 rounded font-semibold text-[11px]">Antrean</span>
                                        @elseif ($order->status === 'Dicuci')
                                            <span class="bg-blue-100 text-blue-800 border border-blue-200 px-2 py-0.5 rounded font-semibold text-[11px]">Dicuci</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 border border-green-200 px-2 py-0.5 rounded font-semibold text-[11px]">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="text-xs border-gray-300 rounded py-1 pl-2 pr-6">
                                                <option value="Antrean" {{ $order->status === 'Antrean' ? 'selected' : '' }}>Antrean</option>
                                                <option value="Dicuci" {{ $order->status === 'Dicuci' ? 'selected' : '' }}>Dicuci</option>
                                                <option value="Selesai" {{ $order->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-2.5 py-1 rounded text-xs transition">
                                                Ubah
                                            </button>
                                        </form>
                                    </td>
                                    <td class="p-3 whitespace-nowrap text-center">
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">Belum ada transaksi pesanan yang masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>