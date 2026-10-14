<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Tarif & Layanan Laundry') }}
        </h2>
    </x-slot>

    <div style="padding: 20px; font-family: Arial, sans-serif;">

        <div style="margin-bottom: 15px;">
            <a href="{{ route('services.create') }}" style="padding: 5px 12px; background: #eee; border: 1px solid #000; text-decoration: none; color: #000; font-weight: bold;">
                [+] Tambah Layanan Baru
            </a>
        </div>

        <h3>Daftar Seluruh Layanan:</h3>

        <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #dddddd; text-align: left;">
                    <th>ID</th>
                    <th>Nama Layanan</th>
                    <th>Kategori</th>
                    <th>Tarif / Harga</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td><b>{{ $service->service_name }}</b></td>
                        <td>{{ $service->category }}</td>
                        <td>Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}</td>
                        <td>Per {{ $service->unit_type }}</td>
                        <td>{{ $service->description ?: '-' }}</td>
                        <td>
                            <a href="{{ route('services.edit', $service->id) }}" style="padding: 2px 8px; background: #eee; border: 1px solid #000; color: #000; text-decoration: none;">Edit</a>
                            
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 2px 8px; background: #eee; border: 1px solid #000; cursor: pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Belum ada layanan yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $services->links() }}
        </div>

    </div>
</x-app-layout>