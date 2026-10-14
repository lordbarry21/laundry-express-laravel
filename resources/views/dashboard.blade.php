<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Rekap Transaksi Laundry') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola antrean cuci kilat, update status progres pengerjaan, dan pantau ringkasan transaksi.
                </p>
            </div>
            <div>
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Kelola Tarif Layanan &rarr;
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. KARTU STATISTIK RINGKASAN TRANSAKSI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-gray-600">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pesanan</div>
                    <div class="mt-2 text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Semua transaksi masuk</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-yellow-500">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Antrean</div>
                    <div class="mt-2 text-2xl font-bold text-yellow-700">{{ $stats['antrean'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Menunggu diproses</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-blue-500">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Sedang Dicuci</div>
                    <div class="mt-2 text-2xl font-bold text-blue-700">{{ $stats['dicuci'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Dalam pengerjaan</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-green-500">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wider">Selesai</div>
                    <div class="mt-2 text-2xl font-bold text-green-700">{{ $stats['selesai'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Siap diambil / diantar</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                    <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Omzet Selesai</div>
                    <div class="mt-2 text-lg font-bold text-indigo-700">Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Total cuci tuntas</div>
                </div>
            </div>

            <!-- 2. FILTER STATUS DAN DAFTAR TRANSAKSI -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 mb-4 border-b border-gray-100 gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daftar Transaksi Pesanan Pelanggan</h3>
                        <p class="text-sm text-gray-500">Data tercatat otomatis saat pelanggan mengisi pendaftaran cuci.</p>
                    </div>

                    <!-- Filter Status -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">Filter Status:</span>
                        <a href="{{ route('dashboard') }}" class="px-3 py-1 rounded-full text-xs font-medium {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Antrean']) }}" class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'Antrean' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                            Antrean ({{ $stats['antrean'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Dicuci']) }}" class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'Dicuci' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                            Dicuci ({{ $stats['dicuci'] }})
                        </a>
                        <a href="{{ route('dashboard', ['status' => 'Selesai']) }}" class="px-3 py-1 rounded-full text-xs font-medium {{ request('status') === 'Selesai' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            Selesai ({{ $stats['selesai'] }})
                        </a>
                    </div>
                </div>

                <!-- TABEL DATA PESANAN -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">No. Order</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Pelanggan & WhatsApp</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Layanan & Berat/Qty</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Total Biaya</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status Saat Ini</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Update Status Pengerjaan</th>
                                <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- No. Order & Tanggal -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">#{{ $order->id }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                    </td>

                                    <!-- Pelanggan & WA -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->whatsapp_number)) }}" target="_blank" class="text-xs text-green-600 hover:text-green-800 font-mono flex items-center gap-1 mt-0.5">
                                            <span>WA: {{ $order->whatsapp_number }}</span>
                                        </a>
                                    </td>

                                    <!-- Rincian Layanan -->
                                    <td class="px-4 py-4">
                                        @foreach ($order->orderDetails as $detail)
                                            <div>
                                                <span class="font-medium text-gray-800">{{ $detail->service->service_name ?? 'Layanan Telah Dihapus' }}</span>
                                                <span class="text-xs text-gray-500 block">
                                                    ({{ $detail->service->category ?? '-' }}) &bull; {{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>

                                    <!-- Total Biaya -->
                                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>

                                    <!-- Status Saat Ini (Badge) -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($order->status === 'Antrean')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                Antrean
                                            </span>
                                        @elseif ($order->status === 'Dicuci')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                Dicuci
                                            </span>
                                        @elseif ($order->status === 'Selesai')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Update Status Progres (Alur Kerja Admin) -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-6">
                                                <option value="Antrean" {{ $order->status === 'Antrean' ? 'selected' : '' }}>Antrean</option>
                                                <option value="Dicuci" {{ $order->status === 'Dicuci' ? 'selected' : '' }}>Dicuci</option>
                                                <option value="Selesai" {{ $order->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                            <button type="submit" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold shadow transition">
                                                Ubah
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Tombol Hapus -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pesanan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada transaksi pesanan laundry yang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
