<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Layanan Laundry / Cuci Kilat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000000;
        }
        th {
            background-color: #dddddd;
            text-align: left;
            padding: 6px;
        }
        td {
            padding: 6px;
        }
        input, select, button {
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 4px;
            box-sizing: border-box;
        }
        input[type="text"], input[type="tel"], input[type="number"], select {
            width: 100%;
            border: 1px solid #000000;
        }
        button {
            padding: 6px 14px;
            background-color: #eeeeee;
            border: 1px solid #000000;
            cursor: pointer;
        }
        .form-table td {
            border: none;
            padding: 5px;
        }
        .form-table {
            border: 1px solid #000000;
            max-width: 600px;
            background-color: #fafafa;
        }
        .error-box {
            border: 1px solid red;
            background-color: #ffe6e6;
            color: red;
            padding: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <h1>Sistem Pendaftaran Layanan Laundry / Cuci Kilat</h1>
    <hr>

    @if (session('error'))
        <div class="error-box">
            <b>Error:</b> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <b>Periksa kembali isian:</b>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Form Pendaftaran Pesanan</h2>

    <form action="{{ route('order.store') }}" method="POST">
        @csrf
        <table class="form-table" cellpadding="4" cellspacing="0">
            <tr>
                <td style="width: 200px;"><b>Nama Pelanggan:</b></td>
                <td>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required>
                </td>
            </tr>
            <tr>
                <td><b>Nomor WhatsApp:</b></td>
                <td>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required>
                </td>
            </tr>
            <tr>
                <td><b>Pilih Layanan:</b></td>
                <td>
                    <select name="service_id" id="service_id" required>
                        <option value="">-- Pilih Paket Cuci --</option>
                        @foreach ($services as $srv)
                            <option value="{{ $srv->id }}"
                                data-price="{{ $srv->price_per_unit }}"
                                data-unit="{{ $srv->unit_type }}"
                                {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                {{ $srv->service_name }} ({{ $srv->category }}) - Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }} / {{ $srv->unit_type }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Perkiraan Berat / Jumlah:</b></td>
                <td>
                    <input type="number" name="qty_or_weight" id="qty_or_weight" min="1" value="{{ old('qty_or_weight', 1) }}" required>
                    <span id="unit-text" style="font-size: 12px; color: #333;">(Satuan: Kg / Pcs)</span>
                </td>
            </tr>
            <tr>
                <td><b>Estimasi Total Biaya:</b></td>
                <td>
                    <b id="total-text" style="font-size: 16px;">Rp 0</b>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit">Kirim Pemesanan</button>
                </td>
            </tr>
        </table>
    </form>

    <br>
    <hr>

    <h2>Daftar Paket Layanan & Tarif</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th>Kategori</th>
                <th>Tarif / Harga</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $key => $srv)
                <tr>
                    <td style="text-align: center;">{{ $key + 1 }}</td>
                    <td><b>{{ $srv->service_name }}</b></td>
                    <td>{{ $srv->category }}</td>
                    <td>Rp {{ number_format($srv->price_per_unit, 0, ',', '.') }}</td>
                    <td>Per {{ $srv->unit_type }}</td>
                    <td>{{ $srv->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data layanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        const serviceSelect = document.getElementById('service_id');
        const qtyInput = document.getElementById('qty_or_weight');
        const unitText = document.getElementById('unit-text');
        const totalText = document.getElementById('total-text');

        function hitung() {
            const opt = serviceSelect.options[serviceSelect.selectedIndex];
            if (!opt || !opt.value) {
                totalText.textContent = 'Rp 0';
                unitText.textContent = '(Satuan: Kg / Pcs)';
                return;
            }
            const price = parseInt(opt.getAttribute('data-price')) || 0;
            const unit = opt.getAttribute('data-unit') || '';
            const qty = parseInt(qtyInput.value) || 0;
            unitText.textContent = '(Satuan: ' + unit + ')';
            totalText.textContent = 'Rp ' + (price * qty).toLocaleString('id-ID');
        }

        serviceSelect.addEventListener('change', hitung);
        qtyInput.addEventListener('input', hitung);
        hitung();
    </script>

</body>
</html>