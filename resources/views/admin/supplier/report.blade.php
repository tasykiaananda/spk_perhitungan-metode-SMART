<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Daftar Supplier - {{ date('d-m-Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 40px;
            background: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .meta-info {
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .footer-sign {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .sign-box {
            text-align: center;
            width: 250px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #5B86E5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak Laporan</button>
        <button onclick="window.close()" style="padding: 8px 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 5px;">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Daftar Supplier</h1>
        <p>Sistem Pendukung Keputusan Pemilihan Supplier Biji Kopi</p>
        <p style="font-weight: bold;">{{ \App\Models\WebsiteSetting::getByKey('app_name', 'Lacete Coffeeshop') }}</p>
    </div>

    <div class="meta-info">
        <div>
            Tanggal Laporan: <b>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</b>
        </div>
        <div>
            Dicetak Oleh: <b>Administrator</b>
        </div>
    </div>

    <h2>Daftar Supplier Terdaftar</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 80px;" class="text-center">No</th>
                <th style="width: 150px;">ID Supplier</th>
                <th>Nama Supplier</th>
                <th style="width: 200px;">Tanggal Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alternatifs as $index => $alt)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><b>{{ $alt->id }}</b></td>
                    <td><b>{{ $alt->nama }}</b></td>
                    <td>{{ $alt->created_at ? $alt->created_at->format('d-m-Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data supplier.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Mengetahui,</p>
            <p style="margin-top: 60px; font-weight: bold; text-decoration: underline;">Manajer Lacete Coffeeshop</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
