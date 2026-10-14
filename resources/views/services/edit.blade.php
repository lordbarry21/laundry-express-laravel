<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Paket Layanan: ') }} {{ $service->service_name }}
            </h2>
            <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">
                &larr; Kembali ke Daftar Layanan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Layanan -->
                    <div>
                        <x-input-label for="service_name" :value="__('Nama Paket Layanan')" />
                        <x-text-input id="service_name" name="service_name" type="text" class="mt-1 block w-full" :value="old('service_name', $service->service_name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('service_name')" />
                    </div>

                    <!-- Grid: Kategori & Satuan Unit -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori (Enum: Kiloan, Satuan, Express) -->
                        <div>
                            <x-input-label for="category" :value="__('Kategori Layanan')" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Kiloan" {{ old('category', $service->category) === 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                                <option value="Satuan" {{ old('category', $service->category) === 'Satuan' ? 'selected' : '' }}>Satuan</option>
                                <option value="Express" {{ old('category', $service->category) === 'Express' ? 'selected' : '' }}>Express</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>

                        <!-- Jenis Satuan (Enum: Kg, Pcs) -->
                        <div>
                            <x-input-label for="unit_type" :value="__('Jenis Satuan Unit')" />
                            <select id="unit_type" name="unit_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Kg" {{ old('unit_type', $service->unit_type) === 'Kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                                <option value="Pcs" {{ old('unit_type', $service->unit_type) === 'Pcs' ? 'selected' : '' }}>Pcs (Per Potong / Lembar)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('unit_type')" />
                        </div>
                    </div>

                    <!-- Tarif / Harga per Satuan -->
                    <div>
                        <x-input-label for="price_per_unit" :value="__('Tarif / Harga per Unit (Rupiah)')" />
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <x-text-input id="price_per_unit" name="price_per_unit" type="number" min="0" step="500" class="block w-full pl-10" :value="old('price_per_unit', $service->price_per_unit)" required />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('price_per_unit')" />
                    </div>

                    <!-- Deskripsi Layanan -->
                    <div>
                        <x-input-label for="description" :value="__('Keterangan / Deskripsi Layanan')" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $service->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Perbarui Layanan') }}
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
