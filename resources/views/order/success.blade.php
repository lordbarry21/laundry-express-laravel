<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nota Pemesanan #{{ $order->id }}</title>
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
            margin-bottom: 15px;
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
        button, a.btn {
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 5px 12px;
            background-color: #eeeeee;
            border: 1px solid #000000;
            color: #000000;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .info-table td {
            border: none;
            padding: 4px;
        }
        .info-table {
            border: 1px solid #000000;
            max-width: 500px;
            margin-bottom: 20px;
            background-color: #fafafa;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <h1>Nota Pendaftaran Layanan Laundry</h1>
    <hr>

    <div class="no-print" style="margin-bottom: 15px;">
        <a href="{{ route('order.create') }}" class="btn">&laquo; Buat Pesanan Baru</a>
        <button onclick="window.print()">Cetak Nota</button>
    </div>

    <table class="info-table" cellpadding="4" cellspacing="0">
        <tr>
            <td style="width: 150px;"><b>No. Pesanan:</b></td>
            <td>#{{ $order->id }}</td>
        </tr>
        <tr>
            <td><b>Status Pengerjaan:</b></td>
            <td><b>[{{ $order->status }}]</b></td>
        </tr>
        <tr>
            <td><b>Nama Pelanggan:</b></td>
            <td>{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td><b>Nomor WhatsApp:</b></td>
            <td>{{ $order->whatsapp_number }}</td>
        </tr>
        <tr>
            <td><b>Waktu Transaksi:</b></td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <h3>Rincian Layanan</h3>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Kategori</th>
                <th>Tarif Satuan</th>
                <th>Jumlah / Berat</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $detail)
                <tr>
                    <td>{{ $detail->service->service_name ?? 'Layanan' }}</td>
                    <td>{{ $detail->service->category ?? '-' }}</td>
                    <td>Rp {{ number_format($detail->service->price_per_unit ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $detail->qty_or_weight }} {{ $detail->service->unit_type ?? 'Unit' }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total Biaya:</td>
                <td style="font-weight: bold; font-size: 16px;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="font-size: 13px;">
        <i>Catatan: Silakan simpan nomor pesanan ini untuk konfirmasi saat penyerahan pakaian.</i>
    </p>

</body>
</html>