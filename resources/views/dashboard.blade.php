<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Rekap Transaksi Laundry') }}
        </h2>
    </x-slot>

    <div style="padding: 20px; font-family: Arial, sans-serif;">

        <!-- RINGKASAN DATA STATISTIK (TABEL SEDERHANA TANPA SHADOW) -->
        <h3>Ringkasan Transaksi:</h3>
        <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #dddddd;">
                <th>Total Pesanan</th>
                <th>Antrean</th>
                <th>Sedang Dicuci</th>
                <th>Selesai</th>
                <th>Total Omzet Selesai</th>
            </tr>
            <tr style="text-align: center;">
                <td><b>{{ $stats['total'] }}</b></td>
                <td>{{ $stats['antrean'] }}</td>
                <td>{{ $stats['dicuci'] }}</td>
                <td>{{ $stats['selesai'] }}</td>
                <td><b>Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</b></td>
            </tr>
        </table>

        <!-- FILTER STATUS -->
        <p style="margin-bottom: 15px;">
            <b>Filter Status:</b>
            <a href="{{ route('dashboard') }}" style="margin-right: 10px;">[Semua ({{ $stats['total'] }})]</a>
            <a href="{{ route('dashboard', ['status' => 'Antrean']) }}" style="margin-right: 10px;">[Antrean ({{ $stats['antrean'] }})]</a>
            <a href="{{ route('dashboard', ['status' => 'Dicuci']) }}" style="margin-right: 10px;">[Dicuci ({{ $stats['dicuci'] }})]</a>
            <a href="{{ route('dashboard', ['status' => 'Selesai']) }}">[Selesai ({{ $stats['selesai'] }})]</a>
        </p>

        <!-- TABEL TRANSAKSI -->
        <h3>Daftar Pesanan Laundry Masuk:</h3>
        <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #dddddd; text-align: left;">
                    <th>No. Order</th>
                    <th>Waktu Masuk</th>
                    <th>Nama Pelanggan</th>
                    <th>No. WhatsApp</th>
                    <th>Rincian Layanan</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th>Update Progres Cuci</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><b>#{{ $order->id }}</b></td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td><b>{{ $order->customer_name }}</b></td>
                        <td>{{ $order->whatsapp_number }}</td>
                        <td>
                            @foreach ($order->orderDetails as $detail)
                                <div>
                                    - {{ $detail->service->service_name ?? 'Layanan' }} 
                                    ({{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }})
                                </div>
                            @endforeach
                        </td>
                        <td><b>Rp {{ number_format($order->total_price, 0, ',', '.') }}</b></td>
                        <td><b>[{{ $order->status }}]</b></td>
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                @method('PATCH')
                                <select name="status" style="padding: 2px;">
                                    <option value="Antrean" {{ $order->status === 'Antrean' ? 'selected' : '' }}>Antrean</option>
                                    <option value="Dicuci" {{ $order->status === 'Dicuci' ? 'selected' : '' }}>Dicuci</option>
                                    <option value="Selesai" {{ $order->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <button type="submit" style="padding: 2px 8px; border: 1px solid #000; background: #eee; cursor: pointer;">Ubah</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 2px 8px; border: 1px solid #000; background: #eee; cursor: pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">Belum ada pesanan yang masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $orders->links() }}
        </div>

    </div>
</x-app-layout>