<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Data Layanan</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('services.create') }}" class="bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 text-white px-4 py-2 rounded mb-4 inline-block font-semibold">+ Tambah Layanan</a>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto bg-white border mt-4 shadow-sm rounded-lg">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 border-b text-gray-700 text-sm">
                        <th class="p-3">Nama</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Tarif per Unit</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr class="border-b text-center text-sm">
                        <td class="p-3 font-semibold">{{ $service->service_name }}</td>
                        <td class="p-3">{{ $service->category }}</td>
                        <td class="p-3 font-bold text-green-700">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}</td>
                        <td class="p-3">
                            <a href="{{ route('services.edit', $service->id) }}" class="text-blue-600 hover:underline mr-3 font-semibold">Edit</a>
                            <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-600">Belum ada data layanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    </div>
</x-app-layout>
