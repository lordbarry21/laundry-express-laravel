<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Master Tarif & Layanan Laundry') }}
            </h2>
            <a href="{{ route('services.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2 px-3.5 rounded-md transition">
                + Tambah Layanan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-5">

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border border-gray-200 rounded-md">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase font-semibold">
                            <tr>
                                <th class="p-3">ID</th>
                                <th class="p-3">Nama Layanan</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Tarif / Harga</th>
                                <th class="p-3">Satuan</th>
                                <th class="p-3">Keterangan</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($services as $service)
                                <tr>
                                    <td class="p-3 font-mono text-gray-500">{{ $service->id }}</td>
                                    <td class="p-3 font-semibold text-gray-900">{{ $service->service_name }}</td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($service->category === 'Kiloan')
                                            <span class="bg-blue-100 text-blue-800 border border-blue-200 px-2 py-0.5 rounded font-medium text-[11px]">Kiloan</span>
                                        @elseif ($service->category === 'Express')
                                            <span class="bg-amber-100 text-amber-800 border border-amber-200 px-2 py-0.5 rounded font-medium text-[11px]">Express</span>
                                        @else
                                            <span class="bg-purple-100 text-purple-800 border border-purple-200 px-2 py-0.5 rounded font-medium text-[11px]">Satuan</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-bold text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-gray-600 whitespace-nowrap">per {{ $service->unit_type }}</td>
                                    <td class="p-3 text-gray-600">{{ $service->description ?: '-' }}</td>
                                    <td class="p-3 whitespace-nowrap text-center space-x-2">
                                        <a href="{{ route('services.edit', $service->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded text-xs font-semibold transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded text-xs font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">Belum ada layanan yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $services->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>