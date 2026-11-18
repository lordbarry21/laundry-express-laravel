<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Layanan</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if($errors->any())
            <ul class="mb-4 list-disc rounded bg-red-100 p-4 pl-8 text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('services.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            <div class="mb-4">
                <label for="service_name" class="block font-medium mb-1">Nama Layanan</label>
                <input type="text" id="service_name" name="service_name" value="{{ old('service_name') }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label for="category" class="block font-medium mb-1">Kategori</label>
                <select id="category" name="category" class="w-full border rounded p-2" required>
                    <option value="Kiloan" @selected(old('category') === 'Kiloan')>Kiloan</option>
                    <option value="Satuan" @selected(old('category') === 'Satuan')>Satuan</option>
                    <option value="Express" @selected(old('category') === 'Express')>Express</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="unit_type" class="block font-medium mb-1">Satuan</label>
                <select id="unit_type" name="unit_type" class="w-full border rounded p-2" required>
                    <option value="Kg" @selected(old('unit_type') === 'Kg')>Kg</option>
                    <option value="Pcs" @selected(old('unit_type') === 'Pcs')>Pcs</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="price_per_unit" class="block font-medium mb-1">Tarif per Unit (Rp)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" min="0" max="2147483647" value="{{ old('price_per_unit') }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block font-medium mb-1">Deskripsi</label>
                <textarea id="description" name="description" class="w-full border rounded p-2" rows="3" required>{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded font-semibold">Simpan Data</button>
        </form>
    </div>
</x-app-layout>