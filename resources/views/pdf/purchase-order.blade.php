<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .info { width: 100%; margin-bottom: 20px; }
        .info td { vertical-align: top; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <strong>PT. Hayuning Mandiri Perkasa</strong><br>
        Jl. Imam Bonjol No. 119, Metro, Lampung
    </div>

    <div class="title">PURCHASE ORDER</div>

    <table class="info" style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 60%;">
                <strong>Kepada Yth:</strong><br>
                {{ $po->nama_supplier }}
            </td>
            <td style="border: none;">
                <strong>No. PO:</strong> {{ $po->no_po }}<br>
                <strong>Tanggal:</strong> {{ $po->tanggal_po }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Barang</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $index => $item)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $item->bahan?->nama_bahan ?? '-' }}</td>
                <td align="center">{{ $item->jumlah }} {{ $item->satuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Metro, {{ date('d F Y') }}</p>
        <br><br><br>
        <p>Admin</P>
    </div>
</body>
</html>
