<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Member</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            width: 50%; /* Dua kartu per baris */
            padding: 5mm;
            vertical-align: top;
        }
        .card-container {
            width: 85.60mm; /* Lebar standar kartu kredit */
            height: 54.0mm; /* Tinggi standar kartu kredit */
            position: relative;
            background-image: url('{{ public_path($setting->path_kartu_member) }}');
            background-size: cover;
            background-repeat: no-repeat;
            box-sizing: border-box;
            /* border: 1px solid #ddd; */ /* Opsional: Border untuk debugging */
        }
        .logo-container {
            position: absolute;
            top: 5mm;
            left: 5mm;
            display: flex;
            align-items: center;
        }
        .logo-container p {
            margin: 0;
            font-size: 8pt;
            font-weight: bold;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7); /* Optional: Text shadow */
        }
        .logo-container img {
            width: 10mm;
            height: 10mm;
            margin-left: 2mm;
        }
        .info-container {
            position: absolute;
            top: 5mm;
            right: 5mm;
            text-align: right;
        }
        .nama {
            font-size: 10pt;
            font-weight: bold;
            color: #fff;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparan */
            padding: 1mm 2mm;
            border-radius: 2mm;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7); /* Optional: Text shadow */
            display: inline-block;
        }
        .telepon {
            font-size: 8pt;
            color: #fff;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparan */
            padding: 0.5mm 2mm;
            border-radius: 2mm;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.7); /* Optional: Text shadow */
            display: inline-block;
            margin-top: 2mm;
        }
        .barcode-container {
            position: absolute;
            bottom: 5mm;
            left: 50%;
            transform: translateX(-50%);
            width: 25mm;
            height: 25mm;
            background: #fff;
            padding: 2mm;
            border: 1px solid #fff;
        }
        .barcode-container img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <table>
        @foreach ($datamember as $chunk)
            <tr>
                @foreach ($chunk as $member)
                    <td class="text-center">
                        <div class="card-container">
                            <!-- Logo di Kiri Atas -->
                            <div class="logo-container">
                                <p>{{ $setting->nama_perusahaan }}</p>
                                <img src="{{ public_path($setting->path_logo) }}" alt="Logo">
                            </div>
                            <!-- Nama dan Telepon di Kanan Atas -->
                            <div class="info-container">
                                <div class="nama">{{ $member->nama }}</div>
                                <div class="telepon">{{ $member->telepon }}</div>
                            </div>
                            <!-- Barcode di Tengah Bawah -->
                            <div class="barcode-container">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($member->kode_member, 'QRCODE') }}" alt="QR Code" width="25mm" height="25mm">
                            </div>
                        </div>
                    </td>
                @endforeach
                @if ($chunk->count() < 2)
                    <td></td> <!-- Empty cell for symmetry if odd number -->
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>
