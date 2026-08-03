<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .header {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .meta {
            font-size: 12px;
            color: #555;
        }
        table {
            border-collapse: collapse;
        }
        th {
            background-color: #5B86E5;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #000000;
            padding: 8px;
        }
        td {
            border: 1px solid #000000;
            padding: 8px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="4" class="header">LAPORAN HASIL KEPUTUSAN PEMILIHAN SUPPLIER</td>
        </tr>
        <tr>
            <td colspan="4" class="header">METODE SMART (SIMPLE MULTI-ATTRIBUTE RATING TECHNIQUE)</td>
        </tr>
        <tr>
            <td colspan="4" class="text-center meta">Tanggal Ekspor: {{ date('d-m-Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
        <thead>
            <tr>
                <th style="width: 100px;">Peringkat</th>
                <th style="width: 250px;">Nama Supplier</th>
                <th style="width: 150px;">Skor Akhir SMART</th>
                <th style="width: 150px;">Kategori Kelayakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankings as $rank)
                <tr>
                    <td class="text-center">{{ $rank['ranking'] }}</td>
                    <td>{{ $rank['nama'] }}</td>
                    <td class="text-right">{{ number_format($rank['skor'], 4) }}</td>
                    <td>
                        @if($rank['skor'] >= 75)
                            Sangat Layak
                        @elseif($rank['skor'] >= 50)
                            Layak
                        @else
                            Kurang Layak
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
