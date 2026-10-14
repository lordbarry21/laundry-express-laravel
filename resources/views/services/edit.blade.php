<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Paket Layanan: ') }} {{ $service->service_name }}
            </h2>
            <a href="{{ route('services.index') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-5">

                <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="service_name" :value="__('Nama Layanan')" />
                        <x-text-input id="service_name" name="service_name" type="text" class="mt-1 block w-full" :value="old('service_name', $service->service_name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('service_name')" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="category" :value="__('Kategori')" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm" required>
                                <option value="Kiloan" {{ old('category', $service->category) === 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                                <option value="Satuan" {{ old('category', $service->category) === 'Satuan' ? 'selected' : '' }}>Satuan</option>
                                <option value="Express" {{ old('category', $service->category) === 'Express' ? 'selected' : '' }}>Express</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>

                        <div>
                            <x-input-label for="unit_type" :value="__('Satuan Unit')" />
                            <select id="unit_type" name="unit_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm" required>
                                <option value="Kg" {{ old('unit_type', $service->unit_type) === 'Kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                                <option value="Pcs" {{ old('unit_type', $service->unit_type) === 'Pcs' ? 'selected' : '' }}>Pcs (Per Lembar/Potong)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('unit_type')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="price_per_unit" :value="__('Tarif per Unit (Rupiah)')" />
                        <x-text-input id="price_per_unit" name="price_per_unit" type="number" min="0" step="500" class="mt-1 block w-full" :value="old('price_per_unit', $service->price_per_unit)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('price_per_unit')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Keterangan')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm">{{ old('description', $service->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button>
                            {{ __('Perbarui Layanan') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>