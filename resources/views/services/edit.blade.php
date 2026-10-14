<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Paket Layanan: ') }} {{ $service->service_name }}
        </h2>
    </x-slot>

    <div style="padding: 20px; font-family: Arial, sans-serif;">

        <div style="margin-bottom: 15px;">
            <a href="{{ route('services.index') }}" style="padding: 4px 10px; background: #eee; border: 1px solid #000; text-decoration: none; color: #000;">&laquo; Kembali ke Daftar Layanan</a>
        </div>

        @if ($errors->any())
            <div style="border: 1px solid red; background: #ffe6e6; color: red; padding: 8px; margin-bottom: 15px;">
                <b>Periksa error:</b>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; max-width: 600px; background: #fafafa;">
                <tr>
                    <td style="width: 180px;"><b>Nama Layanan:</b></td>
                    <td>
                        <input type="text" name="service_name" value="{{ old('service_name', $service->service_name) }}" style="width: 100%; border: 1px solid #000; padding: 4px;" required autofocus>
                    </td>
                </tr>
                <tr>
                    <td><b>Kategori:</b></td>
                    <td>
                        <select name="category" style="width: 100%; border: 1px solid #000; padding: 4px;" required>
                            <option value="Kiloan" {{ old('category', $service->category) === 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                            <option value="Satuan" {{ old('category', $service->category) === 'Satuan' ? 'selected' : '' }}>Satuan</option>
                            <option value="Express" {{ old('category', $service->category) === 'Express' ? 'selected' : '' }}>Express</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><b>Satuan Unit:</b></td>
                    <td>
                        <select name="unit_type" style="width: 100%; border: 1px solid #000; padding: 4px;" required>
                            <option value="Kg" {{ old('unit_type', $service->unit_type) === 'Kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                            <option value="Pcs" {{ old('unit_type', $service->unit_type) === 'Pcs' ? 'selected' : '' }}>Pcs (Per Lembar/Potong)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><b>Tarif per Unit (Rp):</b></td>
                    <td>
                        <input type="number" name="price_per_unit" value="{{ old('price_per_unit', $service->price_per_unit) }}" min="0" step="500" style="width: 100%; border: 1px solid #000; padding: 4px;" required>
                    </td>
                </tr>
                <tr>
                    <td><b>Keterangan:</b></td>
                    <td>
                        <textarea name="description" rows="4" style="width: 100%; border: 1px solid #000; padding: 4px;">{{ old('description', $service->description) }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" style="padding: 6px 14px; background: #eee; border: 1px solid #000; cursor: pointer;">Perbarui Layanan</button>
                    </td>
                </tr>
            </table>
        </form>

    </div>
</x-app-layout>