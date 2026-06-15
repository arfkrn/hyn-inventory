
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Terpadu Inventori - PT HMP</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #333; line-height: 1.2; }
        .kop-surat { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; text-align: center; }
        .kop-surat h1 { margin: 0; font-size: 16pt; }
        .kop-surat p { margin: 2px 0; font-size: 9pt; font-style: italic; }
        
        .judul-seksi { background-color: #444; color: white; padding: 4px 10px; margin-bottom: 8px; font-weight: bold; font-size: 10pt; clear: both; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th { background-color: #f2f2f2; border: 1px solid #000; padding: 5px; font-size: 8pt; text-transform: uppercase; }
        td { border: 1px solid #000; padding: 4px; font-size: 8pt; word-wrap: break-word; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-gray { background-color: #fafafa; }
        
        /* Indikator Warna untuk Skripsi */
        .warning { color: #d32f2f; font-weight: bold; } /* Merah jika kurang/selisih */
        .success { color: #2e7d32; font-weight: bold; } /* Hijau jika lengkap */
        
        .clearfix::after { content: ""; clear: both; display: table; }
        .ttd-box { float: right; width: 200px; text-align: center; margin-top: 20px; }
        
        /* Ganti halaman otomatis jika data terlalu panjang */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h1>PT. HAYUNING MANDIRI PERKASA</h1>
        <p>Jl. Imam Bonjol No. 119 Kel. Hadimulyo Barat, Metro Pusat, Kota Metro</p>
        <p>Laporan Terpadu Logistik & Inventori Bahan Baku Kemasan</p>
    </div>

    <div class="text-center" style="margin-bottom: 15px;">
        <h2 style="text-decoration: underline; margin: 0; font-size: 12pt;">LAPORAN INVENTORI TERPADU</h2>
        <p>Periode: {{ strtoupper($bulan) }} {{ $tahun }}</p>
    </div>

    {{-- SEKSI 1: MUTASI STOK --}}
    <div class="judul-seksi">I. LAPORAN MUTASI (MASUK & KELUAR)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Bahan Baku</th>
                <th width="10%">Satuan</th>
                <th width="25%">Total Masuk (+)</th>
                <th width="25%">Total Keluar (-)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                <td class="text-center">
                    <strong>{{ number_format($row['masuk'], 0, ',', '.') }}</strong><br>
                    <small style="color: #666;">Terakhir: {{ $row['tgl_masuk'] ?: '-' }}</small>
                </td>
                <td class="text-center">
                    <strong>{{ number_format($row['keluar'], 0, ',', '.') }}</strong><br>
                    <small style="color: #666;">Terakhir: {{ $row['tgl_keluar'] ?: '-' }}</small>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SEKSI 2: ANALISIS PENERIMAAN PO vs REALITA --}}
    <div class="judul-seksi">II. ANALISIS KETIDAKSESUAIAN PENERIMAAN (PO vs FISIK)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Bahan</th>
                <th width="20%">Qty Dipesan (PO)</th>
                <th width="20%">Qty Diterima</th>
                <th width="15%">Selisih</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            @php 
                $selisihPenerimaan = $row['qty_po'] - $row['masuk']; 
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td class="text-center">{{ number_format($row['qty_po'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($row['masuk'], 0, ',', '.') }}</td>
                <td class="text-center {{ $selisihPenerimaan > 0 ? 'warning' : '' }}">
                    {{ $selisihPenerimaan > 0 ? '-' . number_format($selisihPenerimaan, 0, ',', '.') : '0' }}
                </td>
                <td class="text-center">
                    @if($selisihPenerimaan > 0)
                        <span class="warning">KURANG</span>
                    @else
                        <span class="success">SESUAI</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SEKSI 3: STOCK OPNAME --}}
    <div class="judul-seksi">III. LAPORAN PEMERIKSAAN STOK (OPNAME)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Bahan Baku</th>
                <th width="15%">Stok Sistem</th>
                <th width="15%">Stok Fisik</th>
                <th width="10%">Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td class="text-center">{{ number_format($row['stok_sistem'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($row['stok_fisik'], 0, ',', '.') }}</td>
                <td class="text-center {{ $row['selisih'] != 0 ? 'warning' : '' }}">
                    {{ $row['selisih'] > 0 ? '+' : '' }}{{ number_format($row['selisih'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="ttd-box">
            <p>Metro, {{ date('d F Y') }}</p>
            <p>{{ auth()->user()->getRoleNames()->first() }}</p>
            <div style="height: 50px;"></div>
            <p><strong>{{ auth()->user()->name }}</strong></p>
        </div>
    </div>

</body>
</html>