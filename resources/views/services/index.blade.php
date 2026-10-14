<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Master Tarif & Layanan Laundry') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola data paket cucian, kategori (Kiloan, Satuan, Express), serta penentuan tarif per unit (Kg / Pcs).
                </p>
            </div>
            <div>
                <a href="{{ route('services.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow">
                    + Tambah Layanan Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">ID</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nama Layanan</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kategori</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Tarif / Harga</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Satuan</th>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Deskripsi</th>
                                <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($services as $service)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap font-mono text-gray-500">
                                        {{ $service->id }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ $service->service_name }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($service->category === 'Kiloan')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Kiloan
                                            </span>
                                        @elseif ($service->category === 'Satuan')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Satuan
                                            </span>
                                        @elseif ($service->category === 'Express')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Express
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap font-semibold text-gray-800">
                                        Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                        / {{ $service->unit_type }}
                                    </td>
                                    <td class="px-4 py-4 text-gray-500 max-w-xs truncate" title="{{ $service->description }}">
                                        {{ $service->description ?: '-' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center space-x-2">
                                        <a href="{{ route('services.edit', $service->id) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-medium shadow-sm transition">
                                            Edit
                                        </a>

                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini? Transaksi terkait akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium shadow-sm transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data layanan laundry yang terdaftar.
                                    </td>
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
