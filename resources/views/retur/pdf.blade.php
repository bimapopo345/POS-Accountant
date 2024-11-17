<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Retur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header, .footer {
            text-align: center;
        }
        .header h2 {
            margin-bottom: 0;
        }
        .header p {
            margin-top: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        .total {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $retur->penjualan->member->nama_perusahaan ?? 'Arandy Furniture' }}</h2>
        <p>Jl. Contoh No. 123, Kota Contoh</p>
        <p>Telp: 0812-3456-7890</p>
        <hr>
        <h3>Nota Retur</h3>
        <p>ID Retur: {{ $retur->id_retur }}</p>
    </div>

    <div class="info">
        <p><strong>ID Penjualan:</strong> {{ $retur->id_penjualan }}</p>
        <p><strong>Tanggal Retur:</strong> {{ \Carbon\Carbon::parse($retur->tanggal_retur)->translatedFormat('d F Y') }}</p>
        <p><strong>Total Retur:</strong> Rp. {{ number_format($retur->total_retur, 2, ',', '.') }}</p>
        <p><strong>Nilai Neto:</strong> Rp. {{ number_format($retur->nilai_neto, 2, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Jumlah Retur</th>
                <th>Harga Retur</th>
                <th>Subtotal Retur</th>
                <th>Alasan Retur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retur->returDetail as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->produk->kode_produk }}</td>
                <td>{{ $detail->produk->nama_produk }}</td>
                <td>{{ $detail->jumlah_retur }}</td>
                <td>Rp. {{ number_format($detail->harga_retur, 2, ',', '.') }}</td>
                <td>Rp. {{ number_format($detail->subtotal_retur, 2, ',', '.') }}</td>
                <td>{{ $detail->alasan_retur }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <hr>
        <p>Terima kasih atas kepercayaan Anda.</p>
    </div>
</body>
</html>
