<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Layanan Laundry Express</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased">

    <!-- HEADER SEDERHANA -->
    <header class="bg-white border-b border-gray-200 py-4 px-6 mb-6">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-xl font-bold text-gray-800">Sistem Pendaftaran Layanan Laundry Express</h1>
            <p class="text-xs text-gray-500">Pemesanan jasa cuci kiloan, satuan, dan paket express</p>
        </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 pb-12 space-y-6">

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                <b>Pemberitahuan:</b> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                <b class="block mb-1">Silakan periksa inputan berikut:</b>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

            <!-- FORM PENDAFTARAN (KIRI) -->
            <div class="md:col-span-7 bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">
                    Form Pendaftaran Pesanan
                </h2>

                <form action="{{ route('order.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="customer_name" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Nama Pelanggan
                        </label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm"
                            placeholder="Contoh: Budi Santoso" required autofocus>
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Nomor WhatsApp
                        </label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm font-mono"
                            placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div>
                        <label for="service_id" class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                            Pilih Paket Layanan
                        </label>
                        <select name="service_id" id="service_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm" required>
                            <option value="">-- Pilih Paket Cuci --</option>
                            @foreach ($services as $srv)
                                <option value="{{ $srv->id }}"
                                    data-price="{{ $srv->price_per_unit }}"
                                    data-unit="{{ $srv->unit_type }}"
                                    {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                    [{{ $srv->category }}] {{ $srv->service_name }} - Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }} / {{ $srv->unit_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="qty_or_weight" class="block text-xs font-semibold text-gray-700 uppercase">
                                Perkiraan Berat / Jumlah
                            </label>
                            <span id="unit-text" class="text-xs text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-medium border border-indigo-100">
                                Satuan: Kg / Pcs
                            </span>
                        </div>
                        <input type="number" name="qty_or_weight" id="qty_or_weight" min="1" value="{{ old('qty_or_weight', 1) }}"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md text-sm" required>
                    </div>

                    <!-- ESTIMASI TOTAL BIAYA -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-md p-3 flex justify-between items-center">
                        <span class="text-xs font-semibold text-indigo-900 uppercase">Estimasi Total Biaya:</span>
                        <span id="total-text" class="text-lg font-bold text-indigo-600">Rp 0</span>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-md text-sm transition">
                            Kirim Pendaftaran Pesanan
                        </button>
                    </div>
                </form>
            </div>

            <!-- DAFTAR TARIF LAYANAN (KANAN) -->
            <div class="md:col-span-5 bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">
                    Daftar Paket Layanan & Tarif
                </h2>

                <div class="space-y-3">
                    @forelse ($services as $srv)
                        <div class="border border-gray-200 rounded-md p-3 text-sm">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <div class="font-semibold text-gray-900">{{ $srv->service_name }}</div>
                                <span class="text-xs font-bold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                @if ($srv->category === 'Kiloan')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-medium text-[11px] border border-blue-200">Kiloan</span>
                                @elseif ($srv->category === 'Express')
                                    <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-medium text-[11px] border border-amber-200">Express</span>
                                @else
                                    <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded font-medium text-[11px] border border-purple-200">Satuan</span>
                                @endif
                                <span class="text-gray-500">per {{ $srv->unit_type }}</span>
                            </div>
                            @if ($srv->description)
                                <p class="text-xs text-gray-600 mt-2">{{ $srv->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">Belum ada paket layanan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <script>
        const serviceSelect = document.getElementById('service_id');
        const qtyInput = document.getElementById('qty_or_weight');
        const unitText = document.getElementById('unit-text');
        const totalText = document.getElementById('total-text');

        function hitung() {
            const opt = serviceSelect.options[serviceSelect.selectedIndex];
            if (!opt || !opt.value) {
                totalText.textContent = 'Rp 0';
                unitText.textContent = 'Satuan: -';
                return;
            }
            const price = parseInt(opt.getAttribute('data-price')) || 0;
            const unit = opt.getAttribute('data-unit') || '';
            const qty = parseInt(qtyInput.value) || 0;
            unitText.textContent = 'Satuan: ' + unit;
            totalText.textContent = 'Rp ' + (price * qty).toLocaleString('id-ID');
        }

        serviceSelect.addEventListener('change', hitung);
        qtyInput.addEventListener('input', hitung);
        hitung();
    </script>

</body>
</html>