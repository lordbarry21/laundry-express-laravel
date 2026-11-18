<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Laundry</title>
    <link rel="icon" href="data:,">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Layanan Laundry</h1>
            <p class="text-gray-500 text-sm mt-1">Pilih layanan, isi perkiraan berat atau jumlah, lalu masukkan nomor WhatsApp.</p>
            @guest
                <a href="{{ route('login') }}" class="mt-2 inline-block text-sm text-blue-700 underline">Admin</a>
            @else
                <a href="{{ auth()->user()->can('manage-laundry') ? route('dashboard') : route('profile.edit') }}" class="mt-2 inline-block text-sm text-blue-700 underline">
                    {{ auth()->user()->can('manage-laundry') ? 'Dashboard Admin' : 'Akun Saya' }}
                </a>
            @endguest
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button type="button" onclick="filterCategory('all', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md">Semua Layanan</button>
            <button type="button" onclick="filterCategory('Kiloan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">Kiloan</button>
            <button type="button" onclick="filterCategory('Satuan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">Satuan</button>
            <button type="button" onclick="filterCategory('Express', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">Express</button>
        </div>

        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST">
            @csrf

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">1. Informasi Pemesan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" maxlength="255" required placeholder="Masukkan nama pemesan" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-semibold text-gray-600 mb-1">Nomor WhatsApp</label>
                        <input type="tel" id="whatsapp_number" name="whatsapp_number" required maxlength="16" pattern="\+?[0-9]{8,15}" value="{{ old('whatsapp_number') }}" placeholder="Contoh: 081234567890" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Layanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="service-card bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between" data-category="{{ $service->category }}">
                        <div>
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2.5 py-0.5 rounded">{{ $service->category }}</span>
                                    <span class="font-bold text-green-600">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg item-name">{{ $service->service_name }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $service->description }}</p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t">
                            <label for="service-{{ $service->id }}" class="block text-xs font-semibold text-gray-600 mb-1">
                                {{ $service->unit_type === 'Kg' ? 'Perkiraan berat (Kg)' : 'Jumlah (Pcs)' }}
                            </label>
                            <input type="number" id="service-{{ $service->id }}" name="items[{{ $service->id }}]" min="0" step="1" value="{{ old('items.'.$service->id, 0) }}" data-unit="{{ $service->unit_type }}" data-name="{{ $service->service_name }}" data-price="{{ $service->price_per_unit }}" class="item-qty w-full border rounded-lg px-3 py-1.5 text-center font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-right">
                <button type="button" id="review-order" onclick="showConfirmationModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">Pesan Sekarang</button>
            </div>

            <div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 id="confirm-title" class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Konfirmasi Pesanan</h3>
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between"><span class="font-semibold">Nama:</span> <span id="modalName" class="text-gray-900 font-bold"></span></div>
                        <div class="flex justify-between"><span class="font-semibold">WhatsApp:</span> <span id="modalWhatsapp" class="text-gray-900 font-bold"></span></div>
                    </div>
                    <div class="border-t border-b py-3 mb-4 max-h-48 overflow-y-auto">
                        <p class="font-semibold text-xs text-gray-400 uppercase mb-2">Rincian Item</p>
                        <ul id="modalItemList" class="space-y-2 text-sm"></ul>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-6">
                        <span>Total Pembayaran:</span>
                        <span id="modalTotalPrice" class="text-green-600 text-xl">Rp 0</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" id="cancel-confirmation" onclick="closeConfirmationModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl shadow transition">Ya, Kirim Pesanan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function filterCategory(category, element) {
            document.querySelectorAll('.btn-category').forEach(btn => {
                btn.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border";
            });
            element.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md";
            const cards = document.querySelectorAll('.service-card');
            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                card.style.display = (category === 'all' || cardCategory === category) ? 'flex' : 'none';
            });
        }

        function showConfirmationModal() {
            const form = document.getElementById('orderForm');
            if (!form.reportValidity()) {
                return;
            }

            const itemList = document.getElementById('modalItemList');
            itemList.replaceChildren();
            let grandTotal = 0;

            document.querySelectorAll('.item-qty').forEach(input => {
                const quantity = Number(input.value);
                if (quantity <= 0) {
                    return;
                }

                const subtotal = quantity * Number(input.dataset.price);
                grandTotal += subtotal;

                const item = document.createElement('li');
                item.textContent = `${input.dataset.name} (${quantity} ${input.dataset.unit}) · Rp ${subtotal.toLocaleString('id-ID')}`;
                itemList.appendChild(item);
            });

            if (!itemList.children.length) {
                alert('Pilih minimal satu layanan.');
                return;
            }

            document.getElementById('modalName').textContent = form.elements.customer_name.value.trim();
            document.getElementById('modalWhatsapp').textContent = form.elements.whatsapp_number.value.trim();
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            const modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('cancel-confirmation').focus();
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('flex');
            document.getElementById('review-order').focus();
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>
