<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Layanan Laundry / Cuci Kilat Express</title>

    <!-- Fonts figtree (bawaan resmi Laravel Breeze) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Asset CSS & JS Bawaan Laravel Breeze via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <!-- NAVBAR USER / PELANGGAN -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow">
                        L
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 leading-tight">Laundry Express</h1>
                        <p class="text-xs text-gray-500">Layanan Cuci Kiloan & Dry Cleaning Kilat</p>
                    </div>
                </div>

                <!-- Menu / Link Admin -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-xs font-semibold uppercase tracking-wider shadow transition">
                            Masuk Dashboard Admin &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-700 hover:text-indigo-600 px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            Login Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA HALAMAN USER -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Banner Pengantar -->
            <div class="bg-indigo-700 rounded-2xl shadow-lg p-6 sm:p-8 text-white">
                <div class="max-w-3xl">
                    <span class="inline-block px-3 py-1 bg-indigo-800 bg-opacity-70 text-indigo-100 rounded-full text-xs font-medium tracking-wide mb-3">
                        Pemesanan Jasa Cuci & Setrika Cepat
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Formulir Pendaftaran Cuci Kilat & Dry Cleaning
                    </h2>
                    <p class="mt-2 text-indigo-100 text-sm sm:text-base leading-relaxed">
                        Pilih paket layanan cucian Anda, tentukan perkiraan berat (Kg) atau jumlah pakaian (Pcs), dan masukkan kontak WhatsApp untuk konfirmasi nota pengerjaan.
                    </p>
                </div>
            </div>

            <!-- Pesan Notifikasi Flash -->
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- 1. FORMULIR PEMESANAN (SISI KIRI / 7 KOLOM) -->
                <div class="lg:col-span-7 bg-white shadow-sm rounded-xl p-6 sm:p-8 border border-gray-100">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h3 class="text-lg font-bold text-gray-900">1. Masukkan Rincian Pemesanan</h3>
                        <p class="text-xs text-gray-500 mt-1">Lengkapi data diri dan rincian pakaian yang ingin dicuci.</p>
                    </div>

                    <form action="{{ route('order.store') }}" method="POST" id="orderForm" class="space-y-6">
                        @csrf

                        <!-- Nama Pelanggan -->
                        <div>
                            <x-input-label for="customer_name" :value="__('Nama Lengkap Pelanggan')" />
                            <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" :value="old('customer_name')" required placeholder="Contoh: Budi Santoso" autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('customer_name')" />
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div>
                            <x-input-label for="whatsapp_number" :value="__('Nomor WhatsApp Aktif')" />
                            <x-text-input id="whatsapp_number" name="whatsapp_number" type="tel" class="mt-1 block w-full font-mono" :value="old('whatsapp_number')" required placeholder="Contoh: 081234567890" />
                            <p class="text-xs text-gray-500 mt-1">Digunakan admin untuk konfirmasi nota cucian dan info saat pakaian selesai.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
                        </div>

                        <!-- Pilihan Paket Layanan (Dropdown) -->
                        <div>
                            <x-input-label for="service_id" :value="__('Pilih Paket Layanan Laundry')" />
                            <select id="service_id" name="service_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('service_id') ? '' : 'selected' }}>-- Pilih Layanan Cuci --</option>
                                @foreach ($services as $srv)
                                    <option value="{{ $srv->id }}"
                                        data-price="{{ $srv->price_per_unit }}"
                                        data-unit="{{ $srv->unit_type }}"
                                        data-category="{{ $srv->category }}"
                                        {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                        [{{ $srv->category }}] {{ $srv->service_name }} - Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }} / {{ $srv->unit_type }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('service_id')" />
                        </div>

                        <!-- Perkiraan Berat / Jumlah Unit -->
                        <div>
                            <div class="flex justify-between items-center">
                                <x-input-label for="qty_or_weight" :value="__('Perkiraan Berat / Jumlah')" />
                                <span id="unitIndicator" class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Satuan: Kg</span>
                            </div>
                            <x-text-input id="qty_or_weight" name="qty_or_weight" type="number" min="1" step="1" class="mt-1 block w-full" :value="old('qty_or_weight', 1)" required />
                            <p class="text-xs text-gray-500 mt-1">
                                Masukkan angka bulat (minimal 1). Jika belum ditimbang pasti, masukkan estimasi berat cucian Anda.
                            </p>
                            <x-input-error class="mt-2" :messages="$errors->get('qty_or_weight')" />
                        </div>

                        <!-- BOX KALKULASI ESTIMASI BIAYA -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-2">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Estimasi Total Biaya</div>
                            <div class="flex justify-between items-baseline">
                                <div>
                                    <span id="calcDetails" class="text-xs text-gray-600">Pilih layanan dan jumlah untuk menghitung</span>
                                </div>
                                <div class="text-2xl font-extrabold text-indigo-600" id="calcTotal">
                                    Rp 0
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit Pendaftaran -->
                        <div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900 text-white font-bold rounded-lg shadow-md transition duration-150">
                                Kirim Pendaftaran Pesanan &rarr;
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. DAFTAR MASTER TARIF & LAYANAN (SISI KANAN / 5 KOLOM) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Daftar Tarif & Paket Laundry</h3>
                        <p class="text-xs text-gray-500 mb-4">Informasi harga resmi jasa cuci, setrika kilat, dan dry cleaning.</p>

                        <div class="space-y-3">
                            @forelse ($services as $service)
                                <div class="border border-gray-200 rounded-lg p-3.5 hover:border-indigo-400 hover:bg-indigo-50/30 transition">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-1
                                                {{ $service->category === 'Express' ? 'bg-amber-100 text-amber-800' : ($service->category === 'Kiloan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                                {{ $service->category }}
                                            </span>
                                            <h4 class="font-semibold text-gray-900 text-sm leading-snug">{{ $service->service_name }}</h4>
                                        </div>
                                        <div class="text-right whitespace-nowrap">
                                            <div class="font-bold text-sm text-gray-900">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}</div>
                                            <div class="text-[11px] text-gray-500">per {{ $service->unit_type }}</div>
                                        </div>
                                    </div>
                                    @if ($service->description)
                                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed">
                                            {{ $service->description }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 py-4 text-center">Belum ada daftar layanan yang tersedia.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Petunjuk Alur Kerja -->
                    <div class="bg-white shadow-sm rounded-xl p-5 border border-gray-100">
                        <h4 class="font-bold text-gray-800 text-sm mb-2">Alur Kerja Pendaftaran Cuci:</h4>
                        <ol class="text-xs text-gray-600 space-y-2 list-decimal list-inside">
                            <li>Isi nama, kontak WhatsApp, dan pilih paket laundry.</li>
                            <li>Tentukan perkiraan berat pakaian atau jumlah satuan.</li>
                            <li>Kirim pesanan dan simpan nota nomor antrean Anda.</li>
                            <li>Pakaian akan dicuci dan status dapat dipantau hingga <strong>Selesai</strong>.</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- FOOTER SEDERHANA -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500 mt-12">
        <p>&copy; {{ date('Y') }} Laundry Express &bull; Sistem Pendaftaran Layanan Cuci Kilat &bull; Framework Laravel</p>
    </footer>

    <!-- SCRIPT KALKULASI REALTIME MURNI JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service_id');
            const qtyInput = document.getElementById('qty_or_weight');
            const unitIndicator = document.getElementById('unitIndicator');
            const calcDetails = document.getElementById('calcDetails');
            const calcTotal = document.getElementById('calcTotal');

            function updateCalculation() {
                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    unitIndicator.textContent = 'Satuan: -';
                    calcDetails.textContent = 'Pilih layanan dan jumlah untuk menghitung';
                    calcTotal.textContent = 'Rp 0';
                    return;
                }

                const price = parseInt(selectedOption.getAttribute('data-price')) || 0;
                const unit = selectedOption.getAttribute('data-unit') || 'Unit';
                const qty = parseInt(qtyInput.value) || 0;

                unitIndicator.textContent = 'Satuan: ' + unit;

                const subtotal = price * qty;
                calcDetails.textContent = `${qty} ${unit} × Rp ${price.toLocaleString('id-ID')}`;
                calcTotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            }

            serviceSelect.addEventListener('change', updateCalculation);
            qtyInput.addEventListener('input', updateCalculation);

            // Inisialisasi awal jika ada old input
            updateCalculation();
        });
    </script>
</body>
</html>
